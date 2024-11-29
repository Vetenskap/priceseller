<?php

namespace Database\Factories;

use App\Models\TelegramLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TelegramLinkFactory extends Factory
{
    protected $model = TelegramLink::class;

    public function definition(): array
    {
        return [
            'token' => Str::random(10),
            'expires_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
