<?php

namespace Database\Factories;

use App\Models\SupplierReportLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierReportLogFactory extends Factory
{
    protected $model = SupplierReportLog::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'level' => $this->faker->word(),
        ];
    }
}
