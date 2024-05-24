<?php

namespace App\Console\Commands\Import;

use App\Models\EmailSupplier;
use App\Models\EmailSupplierStockValue;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportEmailSupplierStockValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:email-supplier-stock-values';

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
        $reader = new \SplFileObject(Storage::path('test/suppliers.csv'));

        while (!$reader->eof()) {
            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                $array = json_decode($row[3]);

                if ($array) {

                    $supplier = Supplier::where('name', $row[1])->first();

                    if ($emailSupplier = EmailSupplier::where('supplier_id', $supplier->id)->first()) {
                        foreach ($array as $item) {

                            EmailSupplierStockValue::updateOrCreate([
                                'email_supplier_id' => $emailSupplier->id,
                                'name' => $item->name
                            ], [
                                'email_supplier_id' => $emailSupplier->id,
                                'name' => $item->name,
                                'value' => $item->value
                            ]);
                        }
                    }

                }
            }
        }
    }
}
