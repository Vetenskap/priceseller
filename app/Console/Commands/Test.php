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

        $test = collect([
            [
                'nm_id' => 1,
                'value' => 3
            ],
            [
                'nm_id' => 2,
                'value' => 6
            ],
            [
                'nm_id' => 3,
                'value' => 7
            ],
        ]);

        $test = $test->filter(fn ($item) => $item['nm_id'] != 3);

        dd($test->all());
    }
}
