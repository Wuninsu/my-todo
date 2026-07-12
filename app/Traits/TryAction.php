<?php

namespace App\Traits;

use App\Exceptions\ActionNotAllowedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Wraps a Livewire action's service call so a thrown exception becomes a
 * logged entry + an error toast instead of an uncaught 500/blank page.
 * Intended for Livewire components only — relies on $this->dispatch().
 */
trait TryAction
{
    protected function tryAction(callable $action, string $failureMessage = 'Something went wrong. Please try again.'): mixed
    {
        try {
            return $action();
        } catch (AuthorizationException $e) {
            Log::channel('services')->warning($e->getMessage(), [
                'component' => static::class,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('toast', type: 'error', message: 'You do not have permission to do that.');

            return null;
        } catch (ActionNotAllowedException $e) {
            Log::channel('services')->notice($e->getMessage(), [
                'component' => static::class,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('toast', type: 'error', message: $e->getMessage());

            return null;
        } catch (Throwable $e) {
            Log::channel('services')->error($e->getMessage(), [
                'component' => static::class,
                'user_id' => auth()->id(),
                'exception' => $e::class,
                'file' => $e->getFile().':'.$e->getLine(),
            ]);

            $this->dispatch('toast', type: 'error', message: $failureMessage);

            return null;
        }
    }
}
