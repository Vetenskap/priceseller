<?php

namespace Database\Factories;

use App\Models\MarketItemRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MarketItemRelationshipFactory extends Factory
{
    protected $model = MarketItemRelationship::class;

    public function definition(): array
    {
        return [
            'external_code' => $this->faker->word(),
            'code' => $this->faker->word(),
            'message' => $this->faker->word(),
            'status' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
