<?php

namespace App\Console\Commands;

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
        dd(WbWarehouseStock::query()
            ->whereHas('wbItem', function (Builder $query) {
                $query->whereHas('item', function (Builder $query) {
                    $query
                        ->where('updated', false)
                        ->orWhere('unload_wb', false)
                        ->where('supplier_id', '9c548271-42d2-4c86-bff0-bdb106154757');
                });
            })
            ->get());
    }
}
