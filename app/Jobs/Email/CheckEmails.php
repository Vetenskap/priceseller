<?php

namespace App\Jobs\Email;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CheckEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $user;
    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->user = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    }
}
