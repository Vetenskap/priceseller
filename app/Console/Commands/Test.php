<?php

namespace App\Console\Commands;

use App\Models\Bundle;
use App\Models\Item;
use App\Models\User;
use App\Models\WbItem;
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
        $collection = collect([
            ['id' => 1, 'unload_wb' => true],
            ['id' => 2, 'unload_wb' => true],
            ['id' => 3, 'unload_wb' => false]
        ]);

        dd(boolval($collection->first(fn (array $item) => !$item['unload_wb'])));
    }
}
