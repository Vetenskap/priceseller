<?php

namespace Database\Factories;

use App\Models\OzonWarehouseSupplierWarehouse;
use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonWarehouseSupplierWarehouseFactory extends Factory
{
    protected $model = OzonWarehouseSupplierWarehouse::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
