<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'timezone' => $data['timezone'],
            'theme' => $data['theme'],
            'version' => $user->version + 1,
            'client_updated_at' => now(),
        ];

        if ($avatar) {
            $payload['avatar'] = $avatar->store('avatars', 'public');
        }

        $user->update($payload);

        return $user;
    }

    public function changePassword(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }

    public function setTheme(User $user, string $theme): void
    {
        $user->update([
            'theme' => $theme,
            'version' => $user->version + 1,
            'client_updated_at' => now(),
        ]);
    }
}
