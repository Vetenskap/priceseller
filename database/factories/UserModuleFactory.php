<?php

namespace Database\Factories;

use App\Models\UserModule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserModuleFactory extends Factory
{
    protected $model = UserModule::class;

    public function definition(): array
    {
        return [
            'enabled' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
