<?php

namespace App\Providers;

use App\Components\EmailClient\EmailClient;
use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Jobs\Email\CheckEmails;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Listeners\ResponseReceivedLogging;
use App\Models\User;
use App\Services\Item\ItemPriceWithCacheService;
use App\Services\Item\ItemPriceServiceInterface;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EmailClient::class, EmailHandlerLaravelImap::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Collection::macro('toJob', function (string $job, ...$arguments) {
            return $this->each(function ($item) use ($arguments, $job) {
                dispatch(new $job($item, ...$arguments));
            });
        });

        Event::listen(ResponseReceived::class, ResponseReceivedLogging::class);

        \LogViewer::auth(function ($request) {
            return $request->user() && $request->user()->isAdmin();
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }
}
