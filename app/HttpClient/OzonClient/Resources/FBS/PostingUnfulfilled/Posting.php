<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use Illuminate\Support\Collection;

class Posting
{
    protected string $posting_number;
    protected int $order_id;
    protected string $order_number;
    protected string $status;
    protected Collection $delivery_method;
    protected string $tracking_number;
    protected string $tpl_integration_type;
    protected string $in_process_at;
    protected string $shipment_date;
    protected ?string $delivering_date = null;
    protected Collection $cancellation;
    protected ?Collection $customer = null;
    protected Collection $products;
    protected Collection $analytics_data;
    protected Collection $financial_data;
    protected ?Collection $translit = null;
    protected bool $is_express;
    protected Collection $requirements;

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

    public function getPostingNumber(): string
    {
        return $this->posting_number;
    }

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function getOrderNumber(): string
    {
        return $this->order_number;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDeliveryMethod(): Collection
    {
        return $this->delivery_method;
    }

    public function getTrackingNumber(): string
    {
        return $this->tracking_number;
    }

    public function getTplIntegrationType(): string
    {
        return $this->tpl_integration_type;
    }

    public function getInProcessAt(): string
    {
        return $this->in_process_at;
    }

    public function getShipmentDate(): string
    {
        return $this->shipment_date;
    }

    public function getDeliveringDate(): string
    {
        return $this->delivering_date;
    }

    public function getCancellation(): Collection
    {
        return $this->cancellation;
    }

    public function getCustomer(): Collection
    {
        return $this->customer;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getAnalyticsData(): Collection
    {
        return $this->analytics_data;
    }

    public function getFinancialData(): Collection
    {
        return $this->financial_data;
    }

    public function getTranslit(): Collection
    {
        return $this->translit;
    }

    public function isIsExpress(): bool
    {
        return $this->is_express;
    }

    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

}
