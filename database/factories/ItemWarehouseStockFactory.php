<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemWarehouseStock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemWarehouseStockFactory extends Factory
{
    protected $model = ItemWarehouseStock::class;

    public function definition(): array
    {
        return [
            'stock' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
