<?php

namespace Modules\Moysklad\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MoyskladFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Moysklad\Models\Moysklad::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'api_key' => $this->faker->word(),
            'enabled_orders' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'clear_order_time' => $this->faker->randomNumber(),
            'diff_price' => $this->faker->randomNumber(),
            'enabled_diff_price' => $this->faker->boolean(),

            'user_id' => User::factory(),
        ];
    }
}

