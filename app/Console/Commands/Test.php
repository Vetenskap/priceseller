<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
use Modules\Moysklad\HttpClient\Resources\Entities\EntityList;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Objects\SalePrice;
use Modules\Moysklad\Models\Moysklad;

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
        $array = ['buyPrice' => [100], 'salePrices' => [123]];

        dd(isset($array['salePrices']));
    }
}
