<?php

namespace App\Listeners;

use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ResponseReceivedLogging
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ResponseReceived $event): void
    {
        if (!$event->response->successful()) {

            RateLimiter::attempt(
                'response_error_' . $event->request->url() . '_' . $event->response->status(),
                2000,
                function () use ($event) {
                    Log::warning('Response is not successful', [
                        'response' => [
                            'status' => $event->response->status(),
                            'body' => $event->response->body(),
                        ],
                        'request' => [
                            'url' => $event->request->url(),
                            'method' => $event->request->method(),
                            'headers' => $event->request->headers(),
                            'body' => $event->request->body(),
                        ]
                    ]);
                },
                21600
            );
        }
    }
}
