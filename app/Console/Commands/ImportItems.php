<?php

namespace App\Console\Commands;

use App\Imports\ItemsImport;
use App\Imports\SupplierImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:items';

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
//        (new ItemsImport)->queue('test/my_store.xlsx', 'public');
//        $this->output->title('Starting import');
//        (new ItemsImport(1))->withOutput($this->output)->import('test/my_store.xlsx', 'public');
//        $this->output->success('Import successful');

        $this->output->title('Starting update');
        (new SupplierImport)->withOutput($this->output)->import('test/test_voshod.csv', 'public');
        $this->output->success('Update successful');
    }
}
