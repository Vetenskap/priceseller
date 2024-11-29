<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'ms_uuid' => $this->faker->uuid(),
            'code' => $this->faker->word(),
            'name' => $this->faker->name(),
            'article' => $this->faker->word(),
            'brand' => $this->faker->word(),
            'price' => $this->faker->randomFloat(),
            'count' => $this->faker->randomNumber(),
            'multiplicity' => $this->faker->randomNumber(),
            'updated' => $this->faker->boolean(),
            'deleted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'unload_wb' => $this->faker->boolean(),
            'unload_ozon' => $this->faker->boolean(),
            'buy_price_reserve' => $this->faker->randomFloat(),
        ];
    }
}
