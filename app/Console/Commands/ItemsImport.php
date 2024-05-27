<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\ItemExcel;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ItemsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:import {path}';

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

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open(Storage::disk('public')->path($this->argument('path')));

        $processedRows = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $this->info('Current row: ' . ++$processedRows);

                try {

                    $itemModel = new ItemExcel($row->toArray());
                    Item::create($itemModel->toArray());

                } catch (\Throwable $e) {

                    dump($e->getMessage());
                    continue;

                }
            }
        }

        $reader->close();
    }
}
