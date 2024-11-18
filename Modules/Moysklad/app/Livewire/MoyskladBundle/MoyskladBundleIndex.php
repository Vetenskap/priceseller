<?php

namespace Modules\Moysklad\Livewire\MoyskladBundle;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Modules\Moysklad\Jobs\MoyskladBundlesApiImport;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladBundleIndex extends BaseComponent
{
    use WithJsNotifications;

    public Moysklad $moysklad;

    public function importApi()
    {
        $status = $this->checkTtlJob(MoyskladBundlesApiImport::getUniqueId($this->moysklad), MoyskladBundlesApiImport::class);

        if ($status) MoyskladBundlesApiImport::dispatch($this->moysklad);
    }

    public function deleteWebhook(array $webhook): void
    {
        $webhook = MoyskladWebhook::find($webhook['id']);

        $service = new MoyskladWebhookService($this->moysklad, $webhook);
        $service->deleteWebhook();
    }

    public function addUpdateWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'bundle')->where('action', 'UPDATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data, true);
    }

    public function addCreateWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'bundle')->where('action', 'CREATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data);
    }

    public function addDeleteWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'bundle')->where('action', 'DELETE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle.moysklad-bundle-index');
    }
}
