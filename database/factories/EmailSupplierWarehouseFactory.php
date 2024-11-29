<?php

namespace Database\Factories;

use App\Models\EmailSupplierWarehouse;
use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailSupplierWarehouseFactory extends Factory
{
    protected $model = EmailSupplierWarehouse::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
