<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

abstract class Entity
{
    public string $id;
    public Collection $data;

    protected function set(Collection $data): void
    {
    }

    public function setId(string $id): void
    {
        $this->id = str_replace(MoyskladClient::BASEURL . static::ENDPOINT, '', $id);
    }

    public function dump(): void
    {
        dump($this->data);
    }

    public function dd(): void
    {
        dd($this->data);
    }

    public function fetch(string $apiKey, array $queryParameters = []): void
    {
        $client = new MoyskladClient($apiKey);
        $this->set($client->get(static::ENDPOINT . $this->id, $queryParameters));
    }

    public function delete(string $apiKey): bool
    {
        $client = new MoyskladClient($apiKey);
        return $client->delete(static::ENDPOINT . $this->id);
    }

    public function put(string $apiKey, array $data): bool
    {
        $client = new MoyskladClient($apiKey);
        return $client->put(static::ENDPOINT . $this->id, $data);
    }
}
