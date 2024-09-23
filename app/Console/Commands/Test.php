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
use Modules\Moysklad\Models\Moysklad;
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
        $ozonMarket = OzonMarket::find('9d07c539-bf26-41cf-a00c-57cecb5f008b');
        $descriptionCategoryTree = new DescriptionCategoryTree();
        $descriptionCategoryTree->fetch($ozonMarket);

        /** @var DescriptionCategory $result */
        $result = null;

        $first = $descriptionCategoryTree->getDescriptionCategories()->first();

        while (!$result) {
            if ($first instanceof DescriptionCategory) {
                if ($first->getChildren() instanceof DescriptionCategoryTree) {
                    if ($first->getChildren()->getDescriptionCategories()->first() instanceof DescriptionCategory && !$first->getChildren()->getDescriptionCategories()->first()->hasChildren()) {
                        $result = $first;
                    }
                }
                $first = $first->getChildren();
            } else {
                $first = $first->getDescriptionCategories()->first();
            }
        }

        /** @var DescriptionCategory $type */
        $type = $result->getChildren()->getDescriptionCategories()->first();

        $type->fetchAttributes($ozonMarket, $result->getDescriptionCategoryId());

        $type->getAttributes()->shift();

        /** @var DescriptionCategoryAttribute $attribute */
        $attribute = $type->getAttributes()->first(fn (DescriptionCategoryAttribute $attribute) => $attribute->getDictionaryId());

        $attribute->fetchValues($ozonMarket, $result->getDescriptionCategoryId(), $type->getTypeId());

        dd($attribute);
    }
}
