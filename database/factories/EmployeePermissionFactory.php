<?php

namespace Database\Factories;

use App\Models\EmployeePermission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmployeePermissionFactory extends Factory
{
    protected $model = EmployeePermission::class;

    public function definition(): array
    {
        return [
            'view' => $this->faker->boolean(),
            'create' => $this->faker->boolean(),
            'update' => $this->faker->boolean(),
            'delete' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
