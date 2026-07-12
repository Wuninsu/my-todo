<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * System-level tags available to every user. They have no owner
     * (user_id is null), cannot be renamed or deleted by anyone through the
     * app (see TagPolicy), and firstOrCreate() here keeps them unique and
     * makes the seeder safe to run more than once.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'urgent', 'color' => '#ef4444'],
            ['name' => 'important', 'color' => '#f59e0b'],
            ['name' => 'work', 'color' => '#6366f1'],
            ['name' => 'personal', 'color' => '#22c55e'],
            ['name' => 'ideas', 'color' => '#0ea5e9'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name'], 'user_id' => null],
                [
                    'uuid' => Str::uuid(),
                    'color' => $tag['color'],
                    'version' => 1,
                    'client_updated_at' => now(),
                ]
            );
        }
    }
}
