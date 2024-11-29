<?php

namespace Database\Factories;

use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierWarehouseFactory extends Factory
{
    protected $model = SupplierWarehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
