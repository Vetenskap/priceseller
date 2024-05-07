<?php

namespace App\Console\Commands;

use App\Models\EmailSupplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

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
        dd(App::isLocal());
//        $user = User::find(1);
//
//        foreach ($user->emails as $email) {
//            foreach ($email->suppliers as $supplier) {
//
//                /** @var EmailSupplier $pivot */
//                $pivot = $supplier->pivot;
//
//                dump(Str::slug($pivot->header_start, '_'));
//                dump(Str::slug($pivot->header_brand, '_'));
//                dump(Str::slug($pivot->header_article_manufacturer, '_'));
//                dump(Str::slug($pivot->header_count, '_'));
//                dump(Str::slug($pivot->header_price, '_'));
//                dump(Str::slug($pivot->header_article_supplier, '_'));
//            }
//        }
    }
}
