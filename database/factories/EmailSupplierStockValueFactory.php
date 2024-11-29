<?php

namespace Database\Factories;

use App\Models\EmailSupplier;
use App\Models\EmailSupplierStockValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailSupplierStockValueFactory extends Factory
{
    protected $model = EmailSupplierStockValue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'value' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
