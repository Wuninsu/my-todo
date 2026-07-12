<?php

namespace App\Console\Commands;

use App\Services\AlertGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateTodoAlerts extends Command
{
    protected $signature = 'todos:generate-alerts';

    protected $description = 'Generate in-app alerts for reminders, due-today, and overdue todos';

    public function handle(AlertGenerationService $alerts): int
    {
        try {
            $alerts->generate();
        } catch (Throwable $e) {
            Log::channel('services')->error($e->getMessage(), [
                'command' => self::class,
                'exception' => $e::class,
                'file' => $e->getFile().':'.$e->getLine(),
            ]);

            $this->error('Failed to generate todo alerts: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
