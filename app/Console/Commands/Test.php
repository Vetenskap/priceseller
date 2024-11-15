<?php

namespace App\Console\Commands;

use App\Jobs\Supplier\PriceUnload;
use App\Models\WbWarehouseStock;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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
        PriceUnload::dispatch(15, 'users/prices/6736c065aaafa_ipTikula.xlsx');
    }
}
