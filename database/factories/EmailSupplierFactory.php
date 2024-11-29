<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailSupplierFactory extends Factory
{
    protected $model = EmailSupplier::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'filename' => $this->faker->word(),
            'header_article' => $this->faker->randomNumber(),
            'header_brand' => $this->faker->randomNumber(),
            'header_price' => $this->faker->randomNumber(),
            'header_count' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'header_warehouse' => $this->faker->randomNumber(),
        ];
    }
}
