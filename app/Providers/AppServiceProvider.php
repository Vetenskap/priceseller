<?php

namespace App\Providers;

use App\Components\EmailClient\EmailClient;
use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Jobs\Email\CheckEmails;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Listeners\ResponseReceivedLogging;
use App\Models\Employee;
use App\Models\User;
use App\Services\Item\ItemPriceWithCacheService;
use App\Services\Item\ItemPriceServiceInterface;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
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

        Collection::macro('toCollectionSpread', function () {
            return $this->map(function ($item) {
                if (!is_array($item)) return $item;
                return collect($item)->toCollectionSpread();
            });
        });

        Collection::macro('getFromDotWithValue', function (string $dot, $value) {
            $parts = collect(explode('.', $dot));
            $firstPart = $parts->first();
            $parts->shift();

            if ($this->has($firstPart)) {
                return collect($this->get($firstPart))->getFromDotWithValue($parts->implode('.'), $value);
            } else {
                return collect($this->firstWhere($firstPart, $value));
            }
        });

        Event::listen(ResponseReceived::class, ResponseReceivedLogging::class);

        \LogViewer::auth(function ($request) {

            return true;

//            if (App::isLocal()) return true;
//
//            return $request->user() && $request->user()->isAdmin();
        });

    }

    public function gates()
    {
        Gate::define('viewPulse', function (User $user) {

            if (App::isLocal()) return true;

            return $user->isAdmin();
        });

        Gate::define('view-email', function (Employee $employee) {
             return $employee->permissions()->where('value', 'emails')->first()?->pivot->view;
        });
    }
}
