<?php

namespace Database\Factories;

use App\Models\EmailPriceItem;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailPriceItemFactory extends Factory
{
    protected $model = EmailPriceItem::class;

    public function definition(): array
    {
        return [
            'article' => $this->faker->word(),
            'brand' => $this->faker->word(),
            'price' => $this->faker->word(),
            'stock' => $this->faker->word(),
            'message' => $this->faker->word(),
            'status' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
