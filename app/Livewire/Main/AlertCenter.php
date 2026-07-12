<?php

namespace App\Livewire\Main;

use App\Services\AlertService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlertCenter extends Component
{
    use TryAction;

    public function markAsRead(string $notificationId, AlertService $alerts): void
    {
        $this->tryAction(fn () => $alerts->markAsRead(Auth::user(), $notificationId), 'Could not update that alert.');
    }

    public function markAllAsRead(AlertService $alerts): void
    {
        $this->tryAction(fn () => $alerts->markAllAsRead(Auth::user()), 'Could not update your alerts.');
    }

    public function clearAll(AlertService $alerts): void
    {
        $this->tryAction(fn () => $alerts->clearAll(Auth::user()), 'Could not clear your alerts.');
    }

    public function openTodo(string $notificationId, int $todoId, AlertService $alerts)
    {
        return $this->tryAction(function () use ($alerts, $notificationId, $todoId) {
            $alerts->markAsRead(Auth::user(), $notificationId);

            return $this->redirect(route('dashboard', ['todo' => $todoId]), navigate: true);
        }, 'Could not open that alert.');
    }

    public function render(AlertService $alerts)
    {
        return view('livewire.main.alert-center', [
            'notifications' => $alerts->recent(Auth::user()),
            'unreadCount' => $alerts->unreadCount(Auth::user()),
        ]);
    }
}
