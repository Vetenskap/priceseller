<?php

namespace App\Console\Commands\Import;

use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportWbMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wb-markets';

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

        $reader = new \SplFileObject(Storage::path('test/market_wbs.csv'));

        while (!$reader->eof()) {

            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                if (isset($users[$row[18]]) && !in_array($row[18], [2, 4])) {

                    if (WbMarket::where('api_key', $row[2])->where('user_id', $users[$row[18]])->exists()) continue;

                    try {
                        WbMarket::updateOrCreate([
                            'api_key' => $row[2],
                            'user_id' => $users[$row[18]]
                        ], [
                            'name' => $row[1],
                            'api_key' => $row[2],
                            'coefficient' => (int) $row[5],
                            'basic_logistics' => (int) $row[6],
                            'price_one_liter' => (int) $row[7],
                            'open' => (int) $row[9],
                            'max_count' => (int) $row[12],
                            'min' => (int) $row[13],
                            'max' => (int) $row[14],
                            'volume' => $row[8],
                            'user_id' => $users[$row[18]]
                        ]);
                    } catch (\Throwable) {

                    }
                }

            }
        }
    }
}
