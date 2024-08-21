<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class WebhookStock extends Entity
{
    const ENDPOINT = '/entity/webhookstock/';

    protected string $accountId;
    protected bool $enabled;
    protected string $url;

    // [stock]
    protected string $stockType;

    // [all, bystore]
    protected string $reportType;

    public function __construct(?Collection $webhookStock = null)
    {
        if ($webhookStock) {
            $this->set($webhookStock);
        }
    }

    public function enable(string $apiKey): bool
    {
        $data = [
            'enabled' => true
        ];

        return $this->put($apiKey, $data);
    }

    public function disable(string $apiKey): bool
    {
        $data = [
            'enabled' => false
        ];

        return $this->put($apiKey, $data);
    }

    public function set(Collection $webhookStock): void
    {
        $this->data = $webhookStock;
        $this->id = $webhookStock->get('id');
        $this->accountId = $webhookStock->get('accountId');
        $this->enabled = $webhookStock->get('enabled');
        $this->url = $webhookStock->get('url');
    }

    public function create(string $apiKey): void
    {
        $client = new MoyskladClient($apiKey);

        $data = [
            'url' => $this->url,
            'enabled' => $this->enabled,
            'reportType' => $this->reportType,
            'stockType' => $this->stockType
        ];

        $this->set($client->post(self::ENDPOINT, $data));
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setReportType(string $reportType): void
    {
        $this->reportType = $reportType;
    }

    public function setStockType(string $stockType): void
    {
        $this->stockType = $stockType;
    }

}
