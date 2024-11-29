<?php

namespace Database\Factories;

use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseSupplier;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonWarehouseSupplierFactory extends Factory
{
    protected $model = OzonWarehouseSupplier::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
