<?php

namespace Database\Factories;

use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbItemFactory extends Factory
{
    protected $model = WbItem::class;

    public function definition(): array
    {
        return [
            'nm_id' => $this->faker->word(),
            'vendor_code' => $this->faker->word(),
            'sku' => $this->faker->word(),
            'sales_percent' => $this->faker->randomFloat(),
            'min_price' => $this->faker->randomNumber(),
            'retail_markup_percent' => $this->faker->randomFloat(),
            'package' => $this->faker->randomFloat(),
            'volume' => $this->faker->randomFloat(),
            'price' => $this->faker->randomNumber(),
            'price_market' => $this->faker->randomNumber(),
            'count' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'subject_id' => $this->faker->randomNumber(),
        ];
    }
}
