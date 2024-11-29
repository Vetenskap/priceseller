<?php

namespace Database\Factories;

use App\Models\ItemAttribute;
use App\Models\ItemAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemAttributeValueFactory extends Factory
{
    protected $model = ItemAttributeValue::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
