<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\OrderFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_name' => $this->faker->word(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'order_description' =>$this->faker->sentence(),
            'amount' => $this->faker->numberBetween(100, 500),
            'is_paid' => false,
        ];
    }
}
