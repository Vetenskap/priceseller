<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\DescriptionCategory;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryAttribute;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryTree;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Services\OzonItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\EditorContent\Services\EditorContentService;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladItemOrderService;
use Modules\Moysklad\Services\MoyskladService;

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
        $ozonItem = OzonItem::find('9d07c247-d70d-4514-b69f-54ad83d75a69');

        /** @var Item $item */
        $item = $ozonItem->ozonitemable;
//        $ozonItem->ozonitemable->moyskladOrders()->where('new', true)->get()
        dd((($ozonItem->ozonitemable->moyskladOrders()->where('new', true)->exists() ? MoyskladItemOrderService::getOrders($ozonItem->ozonitemable)->sum('orders') : 0) * $ozonItem->ozonitemable->multiplicity));
    }
}
