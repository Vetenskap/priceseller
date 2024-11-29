<?php

namespace Database\Factories;

use App\Models\Warehouse;
use App\Models\WbWarehouseUserWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbWarehouseUserWarehouseFactory extends Factory
{
    protected $model = WbWarehouseUserWarehouse::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
