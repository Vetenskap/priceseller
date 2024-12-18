<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonMarketFactory extends Factory
{
    protected $model = OzonMarket::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'client_id' => $this->faker->randomNumber(),
            'api_key' => $this->faker->word(),
            'min_price_coefficient' => $this->faker->randomNumber(),
            'max_price_percent' => $this->faker->randomNumber(),
            'seller_price_percent' => $this->faker->randomNumber(),
            'open' => $this->faker->boolean(),
            'max_count' => $this->faker->randomNumber(),
            'min' => $this->faker->randomNumber(),
            'max' => $this->faker->randomNumber(),
            'seller_price' => $this->faker->boolean(),
            'acquiring' => $this->faker->randomFloat(),
            'last_mile' => $this->faker->randomFloat(),
            'max_mile' => $this->faker->randomNumber(),
            'deleted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'close' => $this->faker->boolean(),
            'minus_stock' => $this->faker->randomNumber(),
            'enabled_price' => $this->faker->boolean(),
            'enabled_update_commissions_in_time' => $this->faker->boolean(),
            'update_commissions_time' => $this->faker->word(),
            'tariff' => $this->faker->word(),
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
