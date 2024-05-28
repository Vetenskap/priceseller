<?php

namespace App\Console\Commands\Import;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Opcodes\LogViewer\Facades\Cache;

class ImportItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:items';

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
            5 => User::where('name', 'Sergiyst')->first()->id,
            6 => User::where('name', 'Владимир')->first()->id,
            7 => User::where('name', 'Вячеслав')->first()->id,
        ];

        $reader = new \SplFileObject(Storage::path('test/items.csv'));

        while (!$reader->eof()) {

            $this->info($reader->key());

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if ($row[12] != 5) continue;

                if ($supplier = Supplier::where('name', $row[2])->first()) {
                    try {

                        $item = Item::updateOrCreate([
                            'user_id' => $users[$row[12]],
                            'code' => $row[1]
                        ], [
                            'user_id' => $users[$row[12]],
                            'code' => $row[1],
                            'supplier_id' => $supplier->id,
                            'article' => $row[3],
                            'brand' => $row[4],
                            'multiplicity' => $row[9],
                        ]);

                        Cache::tags(['item', 'import'])->set($row[0], $item->id);

                    } catch (\Throwable $e) {
                        dd($e->getMessage());
                    }
                }

            }
        }
    }
}
