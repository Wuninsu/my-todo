<?php

namespace App\Livewire\Main;

use App\Services\DeviceService;
use App\Services\ProfileService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileIndex extends Component
{
    use TryAction, WithFileUploads;

    public bool $editing = false;

    public string $name = '';

    public string $email = '';

    public string $timezone = 'UTC';

    public string $theme = 'light';

    public $avatar = null;

    public bool $changingPassword = false;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->timezone = $user->timezone;
        $this->theme = $user->theme;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'timezone' => ['required', 'string', 'max:255'],
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function startEditing(): void
    {
        $this->editing = true;
    }

    public function cancelEditing(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->timezone = $user->timezone;
        $this->theme = $user->theme;
        $this->avatar = null;
        $this->editing = false;
        $this->resetErrorBag();
    }

    public function save(ProfileService $profiles): void
    {
        $validated = $this->validate();

        $this->tryAction(function () use ($profiles, $validated) {
            $profiles->update(Auth::user(), $validated, $this->avatar);

            $this->avatar = null;
            $this->editing = false;

            $this->dispatch('toast', type: 'success', message: 'Profile updated.');
        }, 'Could not update your profile.');
    }

    public function changePassword(ProfileService $profiles): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->tryAction(function () use ($profiles) {
            $profiles->changePassword(Auth::user(), $this->password);

            $this->reset(['current_password', 'password', 'password_confirmation']);
            $this->changingPassword = false;

            $this->dispatch('toast', type: 'success', message: 'Password updated.');
        }, 'Could not update your password.');
    }

    public function revokeDevice(int $deviceId, DeviceService $devices): void
    {
        $this->tryAction(function () use ($devices, $deviceId) {
            $devices->revoke($deviceId);

            $this->dispatch('toast', type: 'success', message: 'Device revoked.');
        }, 'Could not revoke that device.');
    }

    #[Title('My Profile')]
    public function render()
    {
        return view('livewire.main.profile-index', [
            'devices' => Auth::user()->devices()->orderByDesc('last_seen_at')->get(),
        ]);
    }
}
