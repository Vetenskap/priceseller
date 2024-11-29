<?php

namespace Database\Factories;

use App\Models\TableBadItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TableBadItemFactory extends Factory
{
    protected $model = TableBadItem::class;

    public function definition(): array
    {
        return [
            'row' => $this->faker->randomNumber(),
            'attribute' => $this->faker->word(),
            'errors' => $this->faker->words(),
            'values' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
