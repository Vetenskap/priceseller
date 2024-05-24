<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;

class ImportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all';

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
        $this->call('import:users');
        $this->call('import:emails');
        $this->call('import:suppliers');
        $this->call('import:email-suppliers');
        $this->call('import:email-supplier-stock-values');
        $this->call('import:ozon-markets');
        $this->call('import:ozon-warehouses');
        $this->call('import:wb-markets');
        $this->call('import:wb-warehouses');
        $this->call('import:items');
        $this->call('import:ozon-item');
        $this->call('import:wb-item');
    }
}
