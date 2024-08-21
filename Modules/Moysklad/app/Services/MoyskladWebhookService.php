<?php

namespace Modules\Moysklad\Services;

use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\Webhook;
use Modules\Moysklad\HttpClient\Resources\Entities\WebhookStock;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladWebhookService
{
    public Moysklad $moysklad;
    public MoyskladWebhook $webhook;

    /**
     * @param Moysklad $moysklad
     * @param MoyskladWebhook $webhook
     */
    public function __construct(Moysklad $moysklad, MoyskladWebhook $webhook)
    {
        $this->moysklad = $moysklad;
        $this->webhook = $webhook;
    }

    public static function getWarehousesWebhookData(): array
    {
        return collect(config('moysklad.available_webhooks'))->where('action', 'UPDATE')->firstWhere('type', 'warehouses');
    }


    public static function addWarehouseWebhook(Moysklad $moysklad): void
    {
        $data = static::getWarehousesWebhookData();

        if (!$moysklad->webhooks()->where($data)->exists()) {

            $webhook = $moysklad->webhooks()->create($data);

            $url = route('api.moysklad.webhook.index', ['webhook' => $webhook->id]);

            $entityWebhook = new WebhookStock();
            $entityWebhook->setUrl($url);
            $entityWebhook->setStockType('stock');
            $entityWebhook->setReportType('bystore');
            $entityWebhook->setEnabled(true);

            try {
                $entityWebhook->create($moysklad->api_key);
            } catch (\Throwable $e) {
                report($e);
                $webhook->delete();
                return;
            }


            if ($entityWebhook->id) {

                $webhook->moysklad_webhook_uuid = $entityWebhook->id;
                $webhook->save();
            }

        }
    }

    public static function deleteWarehouseWebhook(Moysklad $moysklad): void
    {
        if ($webhook = $moysklad->webhooks()->where(static::getWarehousesWebhookData())->first()) {

            $entityWebhook = new WebhookStock();
            $entityWebhook->setId($webhook->moysklad_webhook_uuid);
            $status = $entityWebhook->delete($moysklad->api_key);

            if ($status) {
                $webhook->delete();
            }
        }
    }

    public static function disableWarehouseWebhook(Moysklad $moysklad): void
    {
        if ($webhook = $moysklad->webhooks()->where(static::getWarehousesWebhookData())->first()) {

            $webhookStockEntity = new WebhookStock();
            $webhookStockEntity->setId($webhook->moysklad_webhook_uuid);

            if ($webhookStockEntity->disable($moysklad->api_key)) {
                $webhook->enabled = false;
                $webhook->save();
            }
        }
    }

    public static function enableWarehouseWebhook(Moysklad $moysklad): void
    {

        if ($webhook = $moysklad->webhooks()->where(static::getWarehousesWebhookData())->first()) {

            $webhookStockEntity = new WebhookStock();
            $webhookStockEntity->setId($webhook->moysklad_webhook_uuid);

            if ($webhookStockEntity->enable($moysklad->api_key)) {
                $webhook->enabled = true;
                $webhook->save();
            }
        }
    }
    public static function createWebhook(Moysklad $moysklad, array $data, bool $diffType = false): void
    {
        if (!$moysklad->webhooks()->where($data)->exists()) {

            $webhook = $moysklad->webhooks()->create($data);

            $url = route('api.moysklad.webhook.index', ['webhook' => $webhook->id]);

            $entityWebhook = new Webhook();
            $entityWebhook->setUrl($url);
            $entityWebhook->setEntityType($data['type']);
            $entityWebhook->setAction($data['action']);

            if ($data['action'] === 'UPDATE') {
                $entityWebhook->setDiffType('FIELDS');
            }

            try {
                $entityWebhook->create($moysklad->api_key);
            } catch (\Throwable $e) {
                report($e);
                $webhook->delete();
                return;
            }

            $webhook->moysklad_webhook_uuid = $entityWebhook->id;
            $webhook->save();

        }
    }

    public function deleteWebhook(): void
    {
        $entityWebhook = new Webhook();
        $entityWebhook->setId($this->webhook->moysklad_webhook_uuid);
        $status = $entityWebhook->delete($this->moysklad->api_key);
        if ($status) {
            $this->webhook->delete();
        }
    }

    public function disableWebhook(): void
    {
        $webhookEntity = new Webhook();
        $webhookEntity->setId($this->webhook->moysklad_webhook_uuid);

        if ($webhookEntity->disable($this->moysklad->api_key)) {
            $this->webhook->enabled = false;
            $this->webhook->save();
        }
    }

    public function enableWebhook(): void
    {
        $webhookEntity = new Webhook();
        $webhookEntity->setId($this->webhook->moysklad_webhook_uuid);

        if ($webhookEntity->enable($this->moysklad->api_key)) {
            $this->webhook->enabled = true;
            $this->webhook->save();
        }
    }
}
