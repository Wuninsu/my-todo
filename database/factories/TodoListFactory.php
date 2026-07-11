<?php

namespace Database\Factories;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TodoList>
 */
class TodoListFactory extends Factory
{
    protected $model = TodoList::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'color' => fake()->safeHexColor(),
            'description' => fake()->optional()->sentence(),
            'is_default' => false,
            'version' => 1,
            'client_updated_at' => now(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'name' => 'My Tasks',
        ]);
    }
}
