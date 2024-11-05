<?php

namespace App\HttpClient\WbClient\Resources\Tariffs;

use App\HttpClient\WbClient\WbClient;
use Illuminate\Support\Collection;

class Commission
{
    CONST ENDPOINT = 'https://common-api.wildberries.ru/api/v1/tariffs/commission';

    CONST TARRIFS = [
        ['name' => 'kgvpMarketplace', 'label' => 'Комиссия по модели Склад продавца — везу на склад WB, %'],
        ['name' => 'kgvpSupplier', 'label' => 'Комиссия по моделям Склад продавца — везу самостоятельно до клиента и Склад продавца — Курьером WB, %'],
        ['name' => 'kgvpSupplierExpress', 'label' => 'Комиссия по модели Склад продавца — везу самостоятельно до клиента экспресс, %'],
        ['name' => 'paidStorageKgvp', 'label' => 'Комиссия по модели Склад WB, %'],
    ];

    protected Collection $report;

    public function setCommission(Collection $report): void
    {
        $this->report = $report->get('report');
    }

    public function fetch(string $api_key): void
    {
        $client = new WbClient($api_key);
        $response = $client->get(self::ENDPOINT);
        $this->setCommission($response->collect()->toCollectionSpread());
    }

    public function getReport(): Collection
    {
        return $this->report;
    }

}
