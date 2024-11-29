<?php

namespace Database\Factories;

use App\Models\ItemSupplierWarehouseStock;
use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemSupplierWarehouseStockFactory extends Factory
{
    protected $model = ItemSupplierWarehouseStock::class;

    public function definition(): array
    {
        return [
            'stock' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
