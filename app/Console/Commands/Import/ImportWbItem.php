<?php

namespace App\Console\Commands\Import;

use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Opcodes\LogViewer\Facades\Cache;

class ImportWbItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wb-item';

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
        $markets = [
            1 => "ИВиКО ВБ",
            26 => "Avtoland",
            28 => "АВТОМИКС",
            34 => "Ивико",
            36 => "ВБ",
            38 => "Ира Канц",
            39 => "САМСОН",
            40 => "Мой Канцелярка",
            41 => "Мой Зима",
            42 => "Мой Туризм",
            43 => "Ира Зима",
            44 => "Ира Лето",
            46 => "Мой Лето",
            47 => "Ира Туризм"
        ];

        $reader = new \SplFileObject(Storage::path('test/wbs.csv'));

        while (!$reader->eof()) {

            $this->info($reader->key());

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if ($itemId = Cache::tags(['item', 'import'])->get($row[16])) {

                    if ($market = WbMarket::where('name', $markets[$row[15]] ?? null)->first()) {
                        if (WbItem::where('nm_id', $row[1])->exists()) continue;

                        WbItem::updateOrCreate([
                            'vendor_code' => $row[2],
                            'wb_market_id' => $market->id
                        ], [
                            'nm_id' => $row[1],
                            'vendor_code' => $row[2],
                            'sku' => $row[3],
                            'sales_percent' => (int)$row[4],
                            'min_price' => (int)$row[5],
                            'retail_markup_percent' => (float)$row[6],
                            'package' => (float)$row[7],
                            'volume' => (float)$row[8],
                            'price_market' => (float)$row[10],
                            'item_id' => $itemId,
                            'wb_market_id' => $market->id
                        ]);
                    }

                }

            }
        }
    }
}
