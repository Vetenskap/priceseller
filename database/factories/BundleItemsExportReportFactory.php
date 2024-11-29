<?php

namespace Database\Factories;

use App\Models\BundleItemsExportReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BundleItemsExportReportFactory extends Factory
{
    protected $model = BundleItemsExportReport::class;

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
