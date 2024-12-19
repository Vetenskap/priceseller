<?php

namespace App\Console\Commands;

use App\Models\EmailSupplier;
use App\Services\EmailSupplierEmailService;
use Illuminate\Console\Command;

class TestUnload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-unload {emailSupplierId} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new EmailSupplierEmailService(EmailSupplier::find($this->argument('emailSupplierId')), $this->argument('path'));
        $service->unload();
    }
}
