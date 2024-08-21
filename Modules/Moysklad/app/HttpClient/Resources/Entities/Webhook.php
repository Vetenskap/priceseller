<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class Webhook extends Entity
{
    const ENDPOINT = '/entity/webhook/';

    protected string $accountId;

    // [CREATE, UPDATE, DELETE, PROCESSED]
    protected string $action;

    // [NONE, FIELDS]
    protected ?string $diffType = null;
    protected bool $enabled;
    protected string $entityType;

    // POST
    protected string $method;
    protected string $url;

    public function __construct(?Collection $webhook = null)
    {
        if ($webhook) {
            $this->set($webhook);
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

    public function set(Collection $webhook): void
    {
        $this->data = $webhook;
        $this->id = $webhook->get('id');
        $this->accountId = $webhook->get('accountId');
        $this->action = $webhook->get('action');
        $this->diffType = $webhook->get('diffType');
        $this->enabled = $webhook->get('enabled');
        $this->entityType = $webhook->get('entityType');
        $this->method = $webhook->get('method');
        $this->url = $webhook->get('url');
    }

    public function create(string $apiKey): void
    {
        $client = new MoyskladClient($apiKey);

        $data = [
            'url' => $this->url,
            'action' => $this->action,
            'entityType' => $this->entityType,
        ];

        if ($this->diffType) {
            $data['diffType'] = $this->diffType;
        }

        $this->set($client->post(self::ENDPOINT, $data));
    }

    public function setEntityType(string $entityType): void
    {
        $this->entityType = $entityType;
    }

    public function setDiffType(?string $diffType): void
    {
        $this->diffType = $diffType;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getDiffType(): ?string
    {
        return $this->diffType;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

}
