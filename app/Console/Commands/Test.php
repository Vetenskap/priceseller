<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\CustomerOrder;
use Modules\Moysklad\HttpClient\Resources\Entities\EntityList;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Entities\Webhook;
use Modules\Moysklad\HttpClient\Resources\Entities\WebhookStock;
use Modules\Moysklad\Models\Moysklad;
use Modules\SamsonApi\HttpClient\Resources\SkuList;
use Modules\SamsonApi\Models\SamsonApi;

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
        dd(Cache::tags(['moysklad', 'product', 'offset'])->get(1));
    }
}
