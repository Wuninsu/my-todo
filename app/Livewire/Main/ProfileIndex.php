<?php

namespace App\Livewire\Main;

use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileIndex extends Component
{
    use WithFileUploads;

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

    public function save(): void
    {
        $validated = $this->validate();

        $user = Auth::user();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'timezone' => $validated['timezone'],
            'theme' => $validated['theme'],
            'version' => $user->version + 1,
            'client_updated_at' => now(),
        ];

        if ($this->avatar) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user->update($data);

        $this->avatar = null;
        $this->editing = false;

        session()->flash('success', 'Profile updated.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => $this->password,
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->changingPassword = false;

        session()->flash('success', 'Password updated.');
    }

    public function revokeDevice(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);
        $this->authorize('delete', $device);

        $device->delete();

        session()->flash('success', 'Device revoked.');
    }

    #[Title('My Profile')]
    public function render()
    {
        return view('livewire.main.profile-index', [
            'devices' => Auth::user()->devices()->orderByDesc('last_seen_at')->get(),
        ]);
    }
}
