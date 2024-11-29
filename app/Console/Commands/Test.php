<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\HttpClient\OzonClient\Resources\FBS\CarriageAvailableList;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\TestTelegramNotification;
use App\Notifications\UserNotification;
use App\Services\WbItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
use Modules\Moysklad\HttpClient\Resources\Entities\Counterparty;
use Modules\Moysklad\HttpClient\Resources\Entities\EntityList;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Objects\SalePrice;
use Modules\Moysklad\Models\Moysklad;
use NotificationChannels\Telegram\TelegramUpdates;

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
    #[NoReturn] public function handle(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create(['user_id' => $user->id, 'supplier_id' => $supplier->id]);
    }
}
