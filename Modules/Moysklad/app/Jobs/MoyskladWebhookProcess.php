<?php

namespace Modules\Moysklad\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookPost;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookStockPost;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookProcessService;

class MoyskladWebhookProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $apiWebhook, public MoyskladWebhook $webhook)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->webhook->type === 'warehouses') {
            $service = new MoyskladWebhookProcessService(new WebhookStockPost($this->apiWebhook), $this->webhook);
        } else {
            $service = new MoyskladWebhookProcessService(new WebhookPost($this->apiWebhook), $this->webhook);
        }

        $service->process();

        $this->webhook->reports()->create(['status' => false]);
    }

    public function failed()
    {
        $this->webhook->reports()->create(['status' => true]);
    }
}