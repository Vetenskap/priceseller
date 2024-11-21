<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbMarketFactory extends Factory
{
    protected $model = WbMarket::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'api_key' => $this->faker->word(),
            'coefficient' => $this->faker->randomFloat(),
            'basic_logistics' => $this->faker->randomNumber(),
            'price_one_liter' => $this->faker->randomNumber(),
            'open' => $this->faker->boolean(),
            'max_count' => $this->faker->randomNumber(),
            'min' => $this->faker->randomNumber(),
            'max' => $this->faker->randomNumber(),
            'volume' => $this->faker->randomNumber(),
            'deleted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'close' => $this->faker->boolean(),
            'minus_stock' => $this->faker->randomNumber(),
            'enabled_update_commissions_in_time' => $this->faker->boolean(),
            'update_commissions_time' => $this->faker->word(),
            'tariff' => $this->faker->word(),
            'enabled_price' => $this->faker->boolean(),
        ];
    }
}
