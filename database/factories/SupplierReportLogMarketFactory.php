<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\SupplierReportLogMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierReportLogMarketFactory extends Factory
{
    protected $model = SupplierReportLogMarket::class;

    public function definition(): array
    {
        return [
            'status' => ReportStatus::cases()[array_rand(ReportStatus::cases())],
            'message' => $this->faker->word(),
            'task_log_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
