<?php

namespace App\Jobs\Email;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Jobs\Supplier\PriceUnload;
use App\Models\Email;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->userId);

        /** @var Email $email */
        foreach ($user->emails as $email) {
            $handler = new EmailHandlerLaravelImap($email->address, $email->password);
            foreach ($email->suppliers as $supplier) {

                $pricePath = $handler->getNewPrice($supplier->pivot->email, $supplier->pivot->filename);

                PriceUnload::dispatchIf((boolean) $pricePath, $supplier->pivot->id, $pricePath);
            }
        }

    }
}
