<?php

namespace Database\Factories;

use App\Models\SupplierReportLogMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierReportLogMarketFactory extends Factory
{
    protected $model = SupplierReportLogMarket::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'message' => $this->faker->word(),
            'item_id' => $this->faker->word(),
            'task_log' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
