<?php

namespace Database\Factories;

use App\Models\UserBaseSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserBaseSettingFactory extends Factory
{
    protected $model = UserBaseSetting::class;

    public function definition(): array
    {
        return [
            'enabled_use_buy_price_reserve' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
