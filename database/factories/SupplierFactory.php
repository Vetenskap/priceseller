<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'ms_uuid' => $this->faker->uuid(),
            'open' => $this->faker->boolean(),
            'use_brand' => $this->faker->boolean(),
            'deleted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'unload_without_price' => $this->faker->boolean(),
        ];
    }
}
