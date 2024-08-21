<?php

namespace Modules\Moysklad\HttpClient\Resources\Reports;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class StocksByStore
{
    const ENDPOINT = '/report/stock/bystore/current';

    protected ?array $queryParameters = [];
    protected Collection $stocks;

    /**
     * @param array|null $queryParameters
     */
    public function __construct(?array $queryParameters = [])
    {
        $this->queryParameters = $queryParameters;
    }

    public function fetchStocks(string $apiKey): void
    {
        $client = new MoyskladClient($apiKey);

        $this->stocks = $client->get(self::ENDPOINT, $this->queryParameters)->toCollectionSpread();
    }

    public function fetchStocksByStore(string $apiKey, string $storeId): void
    {
        $this->queryParameters['filter'] = 'storeId=' . $storeId;

        $client = new MoyskladClient($apiKey);

        $this->stocks = $client->get(self::ENDPOINT, $this->queryParameters)->toCollectionSpread();
    }

    public function getQueryParameters(): ?array
    {
        return $this->queryParameters;
    }

    public function getStocks(): Collection
    {
        return $this->stocks;
    }



}
