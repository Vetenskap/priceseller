<?php

namespace App\Console\Commands\Import;

use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportWbWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wb-warehouses';

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
        $reader = new \SplFileObject(Storage::path('test/market_wbs.csv'));

        while (!$reader->eof()) {

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if ($warehouses = json_decode($row[3])) {

                    if ($market = WbMarket::where('name', $row[1])->first()) {

                        foreach ($warehouses as $warehouse) {
                            WbWarehouse::updateOrCreate([
                                'id' => $warehouse->id,
                            ], [
                                'id' => $warehouse->id,
                                'name' => $warehouse->name,
                                'wb_market_id' => $market->id
                            ]);
                        }

                    }
                }

            }
        }
    }
}
