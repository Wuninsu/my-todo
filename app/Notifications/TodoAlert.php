<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Notifications\Notification;

class TodoAlert extends Notification
{
    public function __construct(
        public string $kind,
        public Todo $todo,
        public string $message,
        public string $dedupeKey,
    ) {}

    /**
     * Database only for now. Add 'mail' / 'broadcast' / a webpush channel
     * here once reminders need to reach the user outside the app itself —
     * the alert is already generated and shaped for it, so wiring up a new
     * channel later doesn't touch how alerts are decided or deduped.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'dedupe_key' => $this->dedupeKey,
            'message' => $this->message,
            'todo_id' => $this->todo->id,
            'todo_uuid' => $this->todo->uuid,
            'todo_title' => $this->todo->title,
            'due_date' => $this->todo->due_date?->toDateString(),
            'reminder_at' => $this->todo->reminder_at?->toIso8601String(),
        ];
    }
}
