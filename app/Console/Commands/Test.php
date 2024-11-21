<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Models\Item;
use App\Models\User;
use App\Notifications\TestTelegramNotification;
use App\Notifications\UserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
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
        $user = User::find(10);
        if (
            $user->userNotification &&
            $user->userNotification->enabled_telegram &&
            $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'export'))->exists()
        ) {
            $user->notify(new UserNotification('тест', 'Экспорт завершен'));
        }
    }
}
