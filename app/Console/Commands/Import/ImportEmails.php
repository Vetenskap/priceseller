<?php

namespace App\Console\Commands\Import;

use App\Models\Email;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:emails';

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
        $users = [
            4 => User::where('name', 'Иван')->first()->id,
            5 => User::where('name', 'Sergiyst')->first()->id,
            6 => User::where('name', 'Владимир')->first()->id,
            7 => User::where('name', 'Вячеслав')->first()->id,
            2 => User::where('name', 'Danil')->first()->id,
        ];

        $reader = new \SplFileObject(Storage::path('test/emails.csv'));

        while (!$reader->eof()) {
            $row = $reader->fgetcsv(';');

            if ($row[0] && $row[0] != 'id') {

                try {
                    Email::updateOrCreate([
                        'address' => $row[2],
                    ], [
                        'name' => $row[1],
                        'address' => $row[2],
                        'password' => $row[3],
                        'open' => (bool) $row[4],
                        'user_id' => $users[$row[5]]
                    ]);
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                }

            }
        }
    }
}
