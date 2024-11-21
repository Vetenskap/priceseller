<?php

namespace Database\Factories;

use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WbWarehouseFactory extends Factory
{
    protected $model = WbWarehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'warehouse_id' => $this->faker->randomNumber(),
        ];
    }
}
