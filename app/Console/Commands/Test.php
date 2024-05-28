<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\TestBroadcast;
use App\Models\EmailSupplier;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Services\SupplierReportService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

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

        dd(
            WbItem::where(function (Builder $query) {
                $query->where('nm_id', 103118301)
                    ->orWhere('sku', 2036851595189);
            })
                ->whereNot('vendor_code', 'smsoptvvs103731')
                ->exists()
        );

        for ($i = 0; $i < 10000; $i++) {
            event(new NotificationEvent(1, 'Тест', 'Тестовое сообщение', 0));
            event(new NotificationEvent(1, 'Тест', 'Тестовое сообщение', 1));
            event(new NotificationEvent(1, 'Тест', 'Тестовое сообщение', 2));
            sleep(1);
        }
    }
}
