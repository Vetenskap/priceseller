<?php

namespace Modules\Moysklad\Livewire\MoyskladItem;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Moysklad\Imports\MoyskladItemsImport;
use Modules\Moysklad\Jobs\MoyskladItemsApiImport;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemAdditionalAttributeLink;
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladService;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladItemIndex extends BaseComponent
{
    use WithFileUploads, WithJsNotifications;

    public Moysklad $moysklad;

    public Collection $assortmentAttributes;
    public $mainAttributesLinks = [];
    public $additionalAttributesLinks = [];

    public function deleteReport($reportId): void
    {
        $report = $this->moysklad->apiItemsReports()->findOrFail($reportId);
        $report->delete();
    }

    public function save(): void
    {
        foreach ($this->mainAttributesLinks as $attribute => $data) {

            $assortmentAttribute = collect($this->assortmentAttributes->firstWhere('name', $data['name']));
            $linkLabel = $assortmentAttribute->get('label');
            $linkType = $assortmentAttribute->get('type');
            $linkName = $linkType === 'metadata' ? $assortmentAttribute->get('label') : $data['name'];

            $this->moysklad->itemMainAttributeLinks()->updateOrCreate([
                'attribute_name' => $attribute
            ], [
                'attribute_name' => $attribute,
                'link' => $data['name'],
                'link_name' => $linkName,
                'link_label' => $linkLabel,
                'type' => $linkType,
                'user_type' => $data['user_type']
            ]);
        }

        foreach ($this->additionalAttributesLinks as $attribute => $data) {

            $assortmentAttribute = collect($this->assortmentAttributes->firstWhere('name', $data['name']));
            $linkLabel = $assortmentAttribute->get('label');
            $linkType = $assortmentAttribute->get('type');
            $linkName = $linkType === 'metadata' ? $assortmentAttribute->get('label') : $data['name'];

            $this->moysklad->itemAdditionalAttributeLinks()->updateOrCreate([
                'item_attribute_id' => $attribute
            ], [
                'item_attribute_id' => $attribute,
                'link' => $data['name'],
                'link_name' => $linkName,
                'link_label' => $linkLabel,
                'type' => $linkType,
                'user_type' => $data['user_type']
            ]);
        }

        $this->addSuccessSaveNotification();
    }

    public function importApi(): void
    {
        $status = $this->checkTtlJob(MoyskladItemsApiImport::getUniqueId($this->moysklad), MoyskladItemsApiImport::class);
        if ($status) MoyskladItemsApiImport::dispatch($this->moysklad);
    }

    public function mount(): void
    {
        $service = new MoyskladService($this->moysklad);
        $this->assortmentAttributes = $service->getAllAssortmentAttributes();
        $this->mainAttributesLinks = $this->moysklad->itemMainAttributeLinks->mapWithKeys(function (MoyskladItemMainAttributeLink $itemMainAttributeLink) {
            return [$itemMainAttributeLink->attribute_name => [
                'name' => $itemMainAttributeLink->link,
                'user_type' => $itemMainAttributeLink->user_type
            ]];
        })->toArray();
        $this->additionalAttributesLinks = $this->moysklad->itemAdditionalAttributeLinks->mapWithKeys(function (MoyskladItemAdditionalAttributeLink $itemAdditionalAttributeLink) {
            return [$itemAdditionalAttributeLink->item_attribute_id => [
                'name' => $itemAdditionalAttributeLink->link,
                'user_type' => $itemAdditionalAttributeLink->user_type
            ]];
        })->toArray();
    }

    public function deleteWebhook(array $webhook): void
    {
        $webhook = MoyskladWebhook::find($webhook['id']);

        $service = new MoyskladWebhookService($this->moysklad, $webhook);
        $service->deleteWebhook();
    }

    public function addUpdateWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'product')->where('action', 'UPDATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data, true);
    }

    public function addCreateWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'product')->where('action', 'CREATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data);
    }

    public function addDeleteWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'product')->where('action', 'DELETE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data);
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item.moysklad-item-index');
    }
}
