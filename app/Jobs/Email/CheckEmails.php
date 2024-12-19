<?php

namespace App\Jobs\Email;

use App\Contracts\EmailHandlerContract;
use App\Jobs\Supplier\PriceUnload;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckEmails implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public int $timeout = 550;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        $this->queue = 'default';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Email $email */
        foreach ($this->user->emails()->where('open', true)->get() as $email) {

            $handler = app(EmailHandlerContract::class);
            foreach ($email->suppliers()->where('open', true)->where('unload_without_price', false)->get() as $supplier) {
                $pricePath = $handler->getNewPrice($supplier->pivot->email, $supplier->pivot->filename, $email->address, $email->password);
                PriceUnload::dispatchIf((boolean)$pricePath, EmailSupplier::findOrFail($supplier->pivot->id), $pricePath);
            }
        }

    }

    public function uniqueId(): string
    {
        return $this->user->id . 'check_emails';
    }
}
