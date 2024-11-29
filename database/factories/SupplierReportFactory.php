<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierReportFactory extends Factory
{
    protected $model = SupplierReport::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->randomNumber(),
            'message' => $this->faker->word(),
            'path' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
