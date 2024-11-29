<?php

namespace Database\Factories;

use App\Models\MarketActionReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MarketActionReportFactory extends Factory
{
    protected $model = MarketActionReport::class;

    public function definition(): array
    {
        return [
            'action' => $this->faker->word(),
            'message' => $this->faker->word(),
            'status' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
