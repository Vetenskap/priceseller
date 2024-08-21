<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class EntityList
{
    protected string $entityClass;
    protected int $limit = 1000;
    protected int $offset = 0;
    protected int $size = 0;
    protected string $apiKey;

    /**
     * @param string $entityClass
     * @param int $limit
     * @param int $offset
     * @param string $apiKey
     */
    public function __construct(string $entityClass, string $apiKey, int $limit = 1000, int $offset = 0)
    {
        $this->entityClass = $entityClass;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->apiKey = $apiKey;
    }


    public function getNext(): Collection
    {
        $client = new MoyskladClient($this->apiKey);

        $result = $client->get($this->entityClass::ENDPOINT, [
            'limit' => $this->limit,
            'offset' => $this->offset
        ]);

        if (!$this->size) {
            $meta = collect($result->get('meta'));
            $this->size = $meta->get('size');
        }

        $rows = collect($result->get('rows'));

        $list = $rows->map(function ($row) {
            return new $this->entityClass(collect($row));
        });

        $this->offset += $this->limit;

        return $list;

    }

    public function hasNext(): bool
    {
        return $this->offset < $this->size;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

}
