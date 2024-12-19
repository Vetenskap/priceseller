<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierReportLogMarket;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('123456789')
        ]);

        $supplier = Supplier::factory()->for($user)->create();

        $report = Task::factory()->for($supplier, 'taskable')->create();

        $log = TaskLog::factory()->for($report)->create();

        SupplierReportLogMarket::factory()->for($log)->create();
    }
}
