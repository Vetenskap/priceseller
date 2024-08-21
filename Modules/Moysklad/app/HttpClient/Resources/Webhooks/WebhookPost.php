<?php

namespace Modules\Moysklad\HttpClient\Resources\Webhooks;

use Illuminate\Support\Collection;

class WebhookPost
{

    protected Collection $events;
    public function __construct(Collection $webhookPost)
    {
        $this->events = collect();

        foreach ($webhookPost->get('events') as $event) {
            $this->events->push(new WebhookEvent(collect($event)));
        }
    }

    public function getEvents(): Collection
    {
        return $this->events;
    }


}
