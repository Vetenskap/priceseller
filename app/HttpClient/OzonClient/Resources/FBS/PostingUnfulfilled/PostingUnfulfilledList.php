<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use App\HttpClient\OzonClient\OzonClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PostingUnfulfilledList
{
    const ENDPOINT = '/v3/posting/fbs/unfulfilled/list';

    protected string $dir = 'ASC';

    protected int $limit = 500;

    protected int $offset = 0;

    protected string $filter_cutoff_from;
    protected string $filter_cutoff_to;
    protected string $filter_delivering_date_from;
    protected string $filter_delivering_date_to;
    protected array $filter_delivery_method_id = [];
    protected array $filter_provider_id = [];
    protected string $filter_status = '';
    protected array $warehouse_id = [];
    protected int $count;

    public function next(string $apiKey, int $clientId): Collection
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
        $result = $client->post(static::ENDPOINT, $data)->collect()->toCollectionSpread();
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

    public function setFilterCutoffFrom(Carbon $filter_cutoff_from): void
    {
        $this->filter_cutoff_from = $filter_cutoff_from->format('Y-m-d\TH:i:s.v') . 'Z';
    }

    public function setFilterCutoffTo(Carbon $filter_cutoff_to): void
    {
        $this->filter_cutoff_to = $filter_cutoff_to->format('Y-m-d\TH:i:s.v') . 'Z';
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

    /** @param string $filter_status One of:
     * acceptance_in_progress — идёт приёмка,
     * awaiting_approve — ожидает подтверждения,
     * awaiting_packaging — ожидает упаковки,
     * awaiting_registration — ожидает регистрации,
     * awaiting_deliver — ожидает отгрузки,
     * arbitration — арбитраж,
     * client_arbitration — клиентский арбитраж доставки,
     * delivering — доставляется,
     * driver_pickup — у водителя,
     * not_accepted — не принят на сортировочном центре.
     */
    public function setFilterStatus(string $filter_status): void
    {
        $this->filter_status = $filter_status;
    }

    public function setWarehouseId(array $warehouse_id): void
    {
        $this->warehouse_id = $warehouse_id;
    }



}
