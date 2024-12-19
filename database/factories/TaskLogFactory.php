<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\TaskLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskLogFactory extends Factory
{
    protected $model = TaskLog::class;

    public function definition(): array
    {
        return [
            'status' => ReportStatus::cases()[array_rand(ReportStatus::cases())],
            'payload' => $this->faker->words(),
            'message' => $this->faker->word(),
            'task_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
