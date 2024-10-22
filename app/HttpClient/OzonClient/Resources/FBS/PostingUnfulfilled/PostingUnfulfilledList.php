<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use App\HttpClient\OzonClient\OzonClient;
use Illuminate\Support\Collection;

class PostingUnfulfilledList
{
    const ENDPOINT = '/v3/posting/fbs/unfulfilled/list';

    protected string $dir = 'asc';

    protected int $limit = 1000;

    protected int $offset = 0;

    protected string $filter_cutoff_from;
    protected string $filter_cutoff_to;
    protected string $filter_delivering_date_from;
    protected string $filter_delivering_date_to;
    protected array $filter_delivery_method_id;
    protected string $filter_provider_id;
    protected string $filter_status;
    protected array $warehouse_id;
    protected int $count;

    public function next(string $apiKey, int $clientId)
    {
        $data = array(
            "dir" => $this->dir,
            "filter" => array(
                "cutoff_from" => $this->filter_cutoff_from,
                "cutoff_to" => $this->filter_cutoff_to,
                "delivery_method_id" => $this->filter_delivery_method_id,
                "provider_id" => $this->filter_provider_id,
                "status" => $this->filter_status,
                "warehouse_id" => $this->warehouse_id
            ),
            "limit" => $this->limit,
            "offset" => $this->offset,
            "with" => array(
                "analytics_data" => true,
                "barcodes" => true,
                "financial_data" => true,
                "translit" => true
            )
        );

        $client = new OzonClient($apiKey, $clientId);
        $result = $client->post(static::ENDPOINT, $data)->toCollectionSpread();
        $this->count = $result->get('result')->get('count');

        return $result->get('result')->get('postings')->map(function (Collection $posting) {
            return new Posting($posting);
        });

    }

    public function hasNext(): bool
    {
        return (bool) $this->count;
    }

    public function setDir(string $dir): void
    {
        $this->dir = $dir;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function setFilterCutoffFrom(string $filter_cutoff_from): void
    {
        $this->filter_cutoff_from = $filter_cutoff_from;
    }

    public function setFilterCutoffTo(string $filter_cutoff_to): void
    {
        $this->filter_cutoff_to = $filter_cutoff_to;
    }

    public function setFilterDeliveringDateFrom(string $filter_delivering_date_from): void
    {
        $this->filter_delivering_date_from = $filter_delivering_date_from;
    }

    public function setFilterDeliveringDateTo(string $filter_delivering_date_to): void
    {
        $this->filter_delivering_date_to = $filter_delivering_date_to;
    }

    public function setFilterDeliveryMethodId(array $filter_delivery_method_id): void
    {
        $this->filter_delivery_method_id = $filter_delivery_method_id;
    }

    public function setFilterProviderId(string $filter_provider_id): void
    {
        $this->filter_provider_id = $filter_provider_id;
    }

    public function setFilterStatus(string $filter_status): void
    {
        $this->filter_status = $filter_status;
    }

    public function setWarehouseId(array $warehouse_id): void
    {
        $this->warehouse_id = $warehouse_id;
    }



}
