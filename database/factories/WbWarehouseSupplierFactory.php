<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseSupplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbWarehouseSupplierFactory extends Factory
{
    protected $model = WbWarehouseSupplier::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
