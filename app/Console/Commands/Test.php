<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Console\Command;
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
        $ms = Moysklad::first();
        $entityList = new EntityList(Webhook::class, $ms->api_key);
        dd($entityList->getNext()->filter(fn (Webhook $webhook) => Str::contains($webhook->getUrl(), '95.129.177.197:56849'))->map(fn (Webhook $webhook) => ['id' => $webhook->id, 'url' => $webhook->getUrl()]));

        $webhook = new Webhook();
        $webhook->setId('44ccdef0-593f-11ef-0a80-037a0011e0e7');
        dd($webhook->delete($ms->api_key));
    }
}
