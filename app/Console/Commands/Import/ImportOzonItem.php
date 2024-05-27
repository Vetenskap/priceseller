<?php

namespace App\Console\Commands\Import;

use App\Models\OzonItem;
use App\Models\OzonMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Opcodes\LogViewer\Facades\Cache;

class ImportOzonItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ozon-item';

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
            2 => "Аксэнд",
            28 => "Avtoland Ozon",
            37 => "АвтоЭксперт",
            38 => "gerc",
            39 => "АВТОМИКС ОЗОН",
            42 => "Мама Канцелярка",
            43 => "Ира Канцелярка",
            44 => "Мама Зима",
            45 => "Мама Туризм",
            46 => "Ира Зима",
            47 => "Ира Лето",
            48 => "Ира Туризм",
            49 => "Мама Лето"
        ];

        $reader = new \SplFileObject(Storage::path('test/ozons.csv'));

        while (!$reader->eof()) {

            $this->info($reader->key());

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if ($itemId = Cache::tags(['item', 'import'])->get($row[19])) {

                    if ($market = OzonMarket::where('name', $markets[$row[18]] ?? null)->first())

                        OzonItem::updateOrCreate([
                            'offer_id' => $row[2],
                            'ozon_market_id' => $market->id
                        ], [
                            'product_id' => $row[1],
                            'offer_id' => $row[2],
                            'min_price_percent' => (int) $row[3],
                            'min_price' => (int) $row[4],
                            'shipping_processing' => (float) $row[5],
                            'direct_flow_trans' => (float) $row[6],
                            'deliv_to_customer' => (float) $row[7],
                            'sales_percent' => (int) $row[8],
                            'price_seller' => (int) $row[10],
                            'price_market' => (int) $row[13],
                            'item_id' => $itemId,
                            'ozon_market_id' => $market->id
                        ]);

                }

            }
        }
    }
}
