<?php

namespace App\Console\Commands\Import;

use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportOzonMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ozon-markets';

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

        $reader = new \SplFileObject(Storage::path('test/market_ozons.csv'));

        while (!$reader->eof()) {

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if (isset($users[$row[22]]) && !in_array($row[22], [2, 4])) {

                    if (OzonMarket::where('client_id', $row[2])->where('user_id', $users[$row[22]])->exists()) {
                        $this->info("Кабинет $row[1]: уже существует");
                        continue;
                    }

                    OzonMarket::updateOrCreate([
                        'client_id' => $row[2],
                        'user_id' => $users[$row[22]]
                    ], [
                        'name' => $row[1],
                        'client_id' => (int) $row[2],
                        'api_key' => $row[3],
                        'min_price_percent' => (int) $row[6],
                        'max_price_percent' => (int) $row[7],
                        'seller_price_percent' => (int) $row[8],
                        'open' => (int) $row[12],
                        'max_count' => (int) $row[16],
                        'min' => (int) $row[17],
                        'max' => (int) $row[18],
                        'seller_price' => (int) $row[15],
                        'acquiring' => (int) $row[9],
                        'last_mile' => (int) $row[10],
                        'max_mile' => (int) $row[11],
                        'user_id' => $users[$row[22]]
                    ]);
                } else {
                    $this->info("Кабинет $row[1]: Не найден юзер - $row[22]");
                }

            }
        }
    }
}
