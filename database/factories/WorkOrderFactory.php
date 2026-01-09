<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['open', 'completed', 'assigned'];
        return [
            'owner_id' => 1,
            'assigned_user_id' => 1,
            'title' => fake()->realText('25'),
            'description' => fake()->realText('150'),
            'address' => fake()->address(),
            'bidding_opens_at' => fake()->date(),
            'bidding_ends_at' =>fake()->date(),
            'status' => $statuses[array_rand($statuses)]
        ];
    }
}
