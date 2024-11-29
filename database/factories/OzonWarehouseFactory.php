<?php

namespace Database\Factories;

use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OzonWarehouseFactory extends Factory
{
    protected $model = OzonWarehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
