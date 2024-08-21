<?php

namespace Modules\Moysklad\HttpClient\Resources\Webhooks;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Reports\StocksByStore;

class WebhookStockPost
{
    protected string $accountId;
    protected string $stockType;
    protected string $reportType;
    protected StocksByStore $stocksByStore;

    public function __construct(Collection $webhookStock)
    {
        $this->accountId = $webhookStock->get('accountId');
        $this->stockType = $webhookStock->get('stockType');
        $this->reportType = $webhookStock->get('reportType');

        $url = $webhookStock->get('reportUrl');
        $url = substr($url, strpos($url, "?") + 1);
        parse_str($url, $queryParameters);

        $stockByStore = new StocksByStore($queryParameters);
        $this->stocksByStore = $stockByStore;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getStockType(): string
    {
        return $this->stockType;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getStocksByStore(): StocksByStore
    {
        return $this->stocksByStore;
    }



}
