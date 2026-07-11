<?php

namespace App\Livewire\Main\Admin;

use App\Models\Device;
use App\Models\SyncLog;
use App\Models\Todo;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

class Overview extends Component
{
    #[Title('Admin Overview')]
    public function render()
    {
        return view('livewire.main.admin.overview', [
            'totalUsers' => User::count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalTodos' => Todo::count(),
            'completedTodos' => Todo::where('status', 'done')->count(),
            'totalDevices' => Device::count(),
            'activeDevices' => Device::where('last_seen_at', '>=', now()->subDays(30))->count(),
            'recentSyncLogs' => SyncLog::with(['user', 'device'])->latest()->take(10)->get(),
        ]);
    }
}
