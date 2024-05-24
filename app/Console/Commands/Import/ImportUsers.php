<?php

namespace App\Console\Commands\Import;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

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
        $reader = new \SplFileObject(Storage::path('test/users.csv'));

        while (!$reader->eof()) {
            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                try {
                    User::updateOrCreate([
                        'email' => $row[2],
                    ], [
                        'name' => $row[1],
                        'email' => $row[2],
                        'password' => $row[4],
                    ]);
                } catch (\Throwable) {

                }

            }
        }
    }
}
