<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Blueprint>
 */
class BlueprintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blueprint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['public', 'private']),
            'php_version' => $this->faker->randomElement(['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0']),
            'wordpress_version' => $this->faker->randomElement(['6.8', '6.7', '6.6', '6.5', '6.4', '6.3', '6.2', '6.1', '6.0', '5.9', '5.8', '5.7', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0']),
            'steps' => [
                [
                    'step' => $this->faker->word(),
                    'plugins' => $this->faker->words(3),
                    'settings' => [
                        'debug' => $this->faker->boolean(),
                        'timezone' => $this->faker->timezone(),
                    ],
                ]
            ],
            'is_anonymous' => $this->faker->boolean(20), // 20% chance of being anonymous
        ];
    }
} 