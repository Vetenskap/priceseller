<?php

namespace App\Jobs\Email;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Jobs\Supplier\PriceUnload;
use App\Models\Email;
use App\Models\User;
use App\Services\SupplierReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Email $email */
        foreach ($this->user->emails()->where('open', true)->get() as $email) {

            $handler = new EmailHandlerLaravelImap($email->address, $email->password);
            foreach ($email->suppliers()->where('open', true)->where('unload_without_price', false)->get() as $supplier) {

                if (!SupplierReportService::get($supplier)) {

                    $pricePath = $handler->getNewPrice($supplier->pivot->email, $supplier->pivot->filename);

                    PriceUnload::dispatchIf((boolean) $pricePath, $supplier->pivot->id, $pricePath);
                }
            }
        }

    }

    public function uniqueId(): string
    {
        return $this->user->id . 'check_emails';
    }
}
