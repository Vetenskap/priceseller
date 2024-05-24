<?php

namespace App\Console\Commands\Import;

use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportEmailSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:email-suppliers';

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
        $userIdToEmailName = [
            3 => 'Восход',
            6 => 'ТАС',
            7 => 'Вячеслав',
        ];

        $reader = new \SplFileObject(Storage::path('test/suppliers.csv'));

        while (!$reader->eof()) {
            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                $supplier = Supplier::where('name', $row[1])->first();

                if (isset($userIdToEmailName[$supplier->user_id])) {
                    try {
                        EmailSupplier::updateOrCreate([
                            'email_id' => Email::where('user_id', $supplier->user_id)->first()->id,
                            'supplier_id' => $supplier->id,
                        ], [
                            'email_id' => Email::where('user_id', $supplier->user_id)->first()->id,
                            'supplier_id' => $supplier->id,
                            'email' => $row[2],
                            'filename' => $row[4],
                            'header_article' => (int) $row[5],
                            'header_brand' => (int) $row[6],
                            'header_price' => (int) $row[9],
                            'header_count' => (int) $row[8],
                        ]);
                    } catch (\Throwable $e) {
                        dd($e->getMessage());
                    }
                }
            }
        }
    }
}
