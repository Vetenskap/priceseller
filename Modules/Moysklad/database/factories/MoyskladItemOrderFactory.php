<?php

namespace Modules\Moysklad\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Moysklad\Models\Moysklad;

class MoyskladItemOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Moysklad\Models\MoyskladItemOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'orders' => $this->faker->randomNumber(),
            'new' => $this->faker->boolean(),
            'item_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'moysklad_id' => MoyskladFactory::new()->create(),
        ];
    }
}

