<?php

namespace Database\Factories;

use App\Models\OzonItem;
use App\Models\OzonMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonItemFactory extends Factory
{
    protected $model = OzonItem::class;

    public function definition(): array
    {
        return [
            'product_id' => $this->faker->randomNumber(),
            'offer_id' => $this->faker->word(),
            'min_price_percent' => $this->faker->randomNumber(),
            'min_price' => $this->faker->randomNumber(),
            'shipping_processing' => $this->faker->randomFloat(),
            'direct_flow_trans' => $this->faker->randomFloat(),
            'deliv_to_customer' => $this->faker->randomFloat(),
            'sales_percent' => $this->faker->randomNumber(),
            'price' => $this->faker->randomNumber(),
            'price_seller' => $this->faker->randomNumber(),
            'price_min' => $this->faker->randomNumber(),
            'price_max' => $this->faker->randomNumber(),
            'price_market' => $this->faker->randomNumber(),
            'count' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
