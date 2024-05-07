<?php

namespace App\Services;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Jobs\Supplier\PriceUnload;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class EmailService
{
    public function allUnload(int $userId)
    {
        $user = User::findOrFail($userId);

        $user->suppliers->each(function (Supplier $supplier) {
            foreach ($supplier->emails as $email) {
                $this->unloadOneSupplier($email->pivot->id);
            }
        });

    }

    public function unloadOneSupplier(string $emailSupplierId)
    {
        $emailSupplier = EmailSupplier::findOrFail($emailSupplierId);

        $email = Email::findOrFail($emailSupplier->email_id);

        $handler = new EmailHandlerLaravelImap($email->address, $email->password, Storage::disk('public'));

        $path = $handler->getNewPrice($emailSupplier->email, $emailSupplier->filename);

        if ($path) {
            $service = new SupplierPriceService($path, $emailSupplier->id);
            $service->handle();
        }
    }
}
