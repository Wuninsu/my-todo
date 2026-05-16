<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [

            [
                'name' => 'Admin User',
                'email' => 'admin@todoflow.test',
                'role' => 'admin',
                'theme' => 'dark',
            ],

            [
                'name' => 'Mohammed Karim',
                'email' => 'karim@todoflow.test',
                'role' => 'user',
                'theme' => 'light',
            ],

            [
                'name' => 'Hafiz Adam',
                'email' => 'adam@todoflow.test',
                'role' => 'user',
                'theme' => 'dark',
            ],
        ];

        foreach ($users as $user) {

            User::create([


                'uuid' => Str::uuid(),
                'device_uuid' => Str::uuid(),
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => 'password',
                'avatar' => null,
                'timezone' => 'UTC',
                'theme' => $user['theme'],
                'role' => $user['role'],

                'is_synced' => false,
                'version' => 1,
                'client_updated_at' => now(),
                'last_synced_at' => null,

                /*SYSTEM*/

                'email_verified_at' => now(),
            ]);
        }
    }
}
