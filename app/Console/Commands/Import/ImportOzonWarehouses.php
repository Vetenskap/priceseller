<?php

namespace App\Console\Commands\Import;

use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportOzonWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ozon-warehouses';

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
        $reader = new \SplFileObject(Storage::path('test/market_ozons.csv'));

        while (!$reader->eof()) {

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if ($warehouses = json_decode($row[4])) {

                    if ($market = OzonMarket::where('name', $row[1])->first()) {

                        foreach ($warehouses as $warehouse) {
                            OzonWarehouse::updateOrCreate([
                                'id' => $warehouse->id,
                            ], [
                                'id' => $warehouse->id,
                                'name' => $warehouse->name,
                                'ozon_market_id' => $market->id
                            ]);
                        }

                    }
                }

            }
        }
    }
}
