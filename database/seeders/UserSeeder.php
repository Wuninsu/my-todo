<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
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

            $createdUser = User::create([
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

                /* SYSTEM */
                'email_verified_at' => now(),
            ]);

            $defaultList = TodoList::create([
                'uuid' => Str::uuid(),
                'user_id' => $createdUser->id,
                'name' => 'My Tasks',
                'is_default' => true,
                'version' => 1,
                'client_updated_at' => now(),
            ]);

            $workList = TodoList::create([
                'uuid' => Str::uuid(),
                'user_id' => $createdUser->id,
                'name' => 'Work',
                'color' => '#0d6efd',
                'version' => 1,
                'client_updated_at' => now(),
            ]);

            $sampleTodos = [
                ['title' => 'Finish offline sync engine', 'description' => 'Build synchronization between local storage and the Laravel API.', 'status' => 'doing', 'priority' => 'high', 'list' => $workList, 'is_favorite' => true],
                ['title' => 'Review pull request', 'description' => null, 'status' => 'todo', 'priority' => 'medium', 'list' => $workList, 'is_favorite' => false],
                ['title' => 'Buy groceries', 'description' => 'Milk, eggs, bread.', 'status' => 'todo', 'priority' => 'low', 'list' => $defaultList, 'is_favorite' => false],
                ['title' => 'Morning workout', 'description' => null, 'status' => 'done', 'priority' => 'medium', 'list' => $defaultList, 'is_favorite' => false],
            ];

            foreach ($sampleTodos as $position => $todo) {
                Todo::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $createdUser->id,
                    'todo_list_id' => $todo['list']->id,
                    'title' => $todo['title'],
                    'description' => $todo['description'],
                    'status' => $todo['status'],
                    'priority' => $todo['priority'],
                    'is_favorite' => $todo['is_favorite'],
                    'completed_at' => $todo['status'] === 'done' ? now() : null,
                    'position' => $position,
                    'version' => 1,
                    'client_updated_at' => now(),
                ]);
            }
        }
    }
}
