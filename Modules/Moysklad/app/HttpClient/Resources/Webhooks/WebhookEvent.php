<?php

namespace Modules\Moysklad\HttpClient\Resources\Webhooks;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\CustomerOrder;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;

class WebhookEvent
{
    protected Product|CustomerOrder $meta;
    protected string $action;
    protected string $accountId;
    protected ?Collection $updatedFields = null;

    public function __construct(Collection $event)
    {
        $meta = collect($event->get('meta'))->toCollectionSpread();

        switch ($meta->get('type')) {
            case 'customerorder':
                $metaItem = new CustomerOrder();
                break;
            default:
                $metaItem = new Product();
                break;
        }

        $metaItem->setId(collect($event->get('meta'))->get('href'));
        $this->meta = $metaItem;

        $this->action = $event->get('action');
        $this->accountId = $event->get('accountId');
        $this->updatedFields = collect($event->get('updatedFields'));
    }

    public function getMeta(): Product|CustomerOrder
    {
        return $this->meta;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getUpdatedFields(): ?Collection
    {
        return $this->updatedFields;
    }


}