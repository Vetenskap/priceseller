<?php

namespace Database\Factories;

use App\Models\WbItem;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbWarehouseStockFactory extends Factory
{
    protected $model = WbWarehouseStock::class;

    public function definition(): array
    {
        return [
            'stock' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
