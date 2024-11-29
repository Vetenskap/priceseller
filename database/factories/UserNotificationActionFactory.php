<?php

namespace Database\Factories;

use App\Models\NotificationAction;
use App\Models\UserNotificationAction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserNotificationActionFactory extends Factory
{
    protected $model = UserNotificationAction::class;

    public function definition(): array
    {
        return [
            'enabled' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
