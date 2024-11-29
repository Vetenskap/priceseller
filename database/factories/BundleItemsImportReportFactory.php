<?php

namespace Database\Factories;

use App\Models\BundleItemsImportReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BundleItemsImportReportFactory extends Factory
{
    protected $model = BundleItemsImportReport::class;

    public function definition(): array
    {
        return [
            'correct' => $this->faker->randomNumber(),
            'error' => $this->faker->randomNumber(),
            'deleted' => $this->faker->randomNumber(),
            'message' => $this->faker->word(),
            'status' => $this->faker->randomNumber(),
            'uuid' => $this->faker->uuid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
