<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WarehousesItemsExportReportFactory extends Factory
{
    protected $model = WarehousesItemsExportReport::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'message' => $this->faker->word(),
            'status' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
