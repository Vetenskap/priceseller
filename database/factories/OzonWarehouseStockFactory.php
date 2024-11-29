<?php

namespace Database\Factories;

use App\Models\OzonItem;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonWarehouseStockFactory extends Factory
{
    protected $model = OzonWarehouseStock::class;

    public function definition(): array
    {
        return [
            'stock' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
