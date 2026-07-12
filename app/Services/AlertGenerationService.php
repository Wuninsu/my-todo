<?php

namespace App\Services;

use App\Models\Todo;
use App\Notifications\TodoAlert;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlertGenerationService
{
    public function generate(): void
    {
        $existingKeys = DB::table('notifications')
            ->where('type', TodoAlert::class)
            ->pluck('data')
            ->map(fn ($data) => json_decode($data, true)['dedupe_key'] ?? null)
            ->filter()
            ->flip();

        $today = Carbon::today();

        $this->alertReminders($existingKeys);
        $this->alertDueToday($existingKeys, $today);
        $this->alertOverdue($existingKeys, $today);
    }

    protected function alertReminders($existingKeys): void
    {
        Todo::query()
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', now())
            ->whereNotIn('status', ['done', 'archived'])
            ->with('user')
            ->each(function (Todo $todo) use ($existingKeys) {
                $key = "reminder:{$todo->id}:{$todo->reminder_at->timestamp}";

                if (! isset($existingKeys[$key]) && $todo->user) {
                    $todo->user->notify(new TodoAlert(
                        kind: 'reminder',
                        todo: $todo,
                        message: "Reminder: \"{$todo->title}\" is due.",
                        dedupeKey: $key,
                    ));
                }
            });
    }

    protected function alertDueToday($existingKeys, Carbon $today): void
    {
        Todo::query()
            ->whereDate('due_date', $today)
            ->whereNotIn('status', ['done', 'archived'])
            ->with('user')
            ->each(function (Todo $todo) use ($existingKeys, $today) {
                $key = "due_today:{$todo->id}:{$today->toDateString()}";

                if (! isset($existingKeys[$key]) && $todo->user) {
                    $todo->user->notify(new TodoAlert(
                        kind: 'due_today',
                        todo: $todo,
                        message: "\"{$todo->title}\" is due today.",
                        dedupeKey: $key,
                    ));
                }
            });
    }

    protected function alertOverdue($existingKeys, Carbon $today): void
    {
        Todo::query()
            ->whereDate('due_date', '<', $today)
            ->whereNotIn('status', ['done', 'archived'])
            ->with('user')
            ->each(function (Todo $todo) use ($existingKeys, $today) {
                $key = "overdue:{$todo->id}:{$today->toDateString()}";

                if (! isset($existingKeys[$key]) && $todo->user) {
                    $todo->user->notify(new TodoAlert(
                        kind: 'overdue',
                        todo: $todo,
                        message: "\"{$todo->title}\" is overdue.",
                        dedupeKey: $key,
                    ));
                }
            });
    }
}
