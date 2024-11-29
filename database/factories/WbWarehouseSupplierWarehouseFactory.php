<?php

namespace Database\Factories;

use App\Models\SupplierWarehouse;
use App\Models\WbWarehouseSupplierWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbWarehouseSupplierWarehouseFactory extends Factory
{
    protected $model = WbWarehouseSupplierWarehouse::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
