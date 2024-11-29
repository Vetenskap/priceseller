<?php

namespace Database\Factories;

use App\Models\ItemsExportReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemsExportReportFactory extends Factory
{
    protected $model = ItemsExportReport::class;

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
