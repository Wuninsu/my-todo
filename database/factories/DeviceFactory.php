<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'device_name' => fake()->randomElement(['iPhone 15', 'Pixel 8', 'Chrome on Windows', 'Safari on Mac']),
            'device_type' => fake()->randomElement(['mobile', 'desktop']),
            'platform' => fake()->randomElement(['iOS', 'Android', 'Windows', 'macOS']),
            'last_seen_at' => now(),
            'version' => 1,
        ];
    }
}
