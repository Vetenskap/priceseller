<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use Illuminate\Support\Collection;

class Posting
{
    protected string $posting_number;

    protected int $order_id;

    protected string $order_number;

    protected string $status;

    protected array $delivery_method;

    protected string $tracking_number;

    protected string $tpl_integration_type;

    protected string $in_process_at;

    protected string $shipment_date;

    protected string $delivering_date;

    protected array $cancellation;

    protected array $customer;

    protected array $products;

    protected array $analytics_data;

    protected array $financial_data;

    protected array $translit;

    protected bool $is_express;

    protected array $requirements;

    public function __construct(Collection $posting)
    {

        $this->posting_number = $posting->get('posting_number');
        $this->order_id = $posting->get('order_id');
        $this->order_number = $posting->get('order_number');
        $this->status = $posting->get('status');
        $this->delivery_method = $posting->get('delivery_method');
        $this->tracking_number = $posting->get('tracking_number');
        $this->tpl_integration_type = $posting->get('tpl_integration_type');
        $this->in_process_at = $posting->get('in_process_at');
        $this->shipment_date = $posting->get('shipment_date');
        $this->delivering_date = $posting->get('delivering_date');
        $this->cancellation = $posting->get('cancellation');
        $this->customer = $posting->get('customer');
        $this->products = $posting->get('products');
        $this->analytics_data = $posting->get('analytics_data');
        $this->financial_data = $posting->get('financial_data');
        $this->translit = $posting->get('translit');
        $this->is_express = $posting->get('is_express');
        $this->requirements = $posting->get('requirements');
    }

}
