<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\HttpClient\WbClient\Resources\Card\Card;
use App\HttpClient\WbClient\Resources\Card\CardList;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use App\Services\WbItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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
        $market = WbMarket::find('9d095e54-1835-4275-b61b-7da0a4b2b82d');
        $list = new CardList($market->api_key);

        do {

            $cards = $list->next();

            $cards->each(function (Card $card) {
                dd($card);
            });

        } while ($list->hasNext());
    }
}
