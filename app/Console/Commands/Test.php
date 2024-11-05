<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\Models\OzonMarket;
use App\Models\WbItem;
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
        $collect = collect(['123key', 'userWarehouses']);

        dd($collect->search('userWarehouses'));
    }
}
