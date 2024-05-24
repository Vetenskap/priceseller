<?php

namespace App\Console\Commands\Import;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:suppliers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = [
            4 => User::where('name', 'Иван')->first()->id,
            5 => User::where('name', 'Sergiyst')->first()->id,
            6 => User::where('name', 'Владимир')->first()->id,
            7 => User::where('name', 'Вячеслав')->first()->id,
            2 => User::where('name', 'Danil')->first()->id,
        ];

        $reader = new \SplFileObject(Storage::path('test/suppliers.csv'));

        while (!$reader->eof()) {
            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                try {
                    Supplier::updateOrCreate([
                        'name' => $row[1],
                        'user_id' => $users[$row[16]],
                    ], [
                        'name' => $row[1],
                        'user_id' => $users[$row[16]],
                        'use_brand' => is_numeric($row[6]),
                        'open' => $row[11],
                    ]);
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                }

            }
        }
    }
}
