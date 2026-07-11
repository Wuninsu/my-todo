<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'todo_list_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => 'todo',
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'is_favorite' => false,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'position' => 0,
            'version' => 1,
            'client_updated_at' => now(),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
            'completed_at' => now(),
        ]);
    }

    public function favorite(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => true,
        ]);
    }
}
