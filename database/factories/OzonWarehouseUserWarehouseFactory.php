<?php

namespace Database\Factories;

use App\Models\OzonWarehouseUserWarehouse;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonWarehouseUserWarehouseFactory extends Factory
{
    protected $model = OzonWarehouseUserWarehouse::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
