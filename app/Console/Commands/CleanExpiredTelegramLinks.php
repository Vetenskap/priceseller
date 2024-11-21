<?php

namespace App\Console\Commands;

use App\Models\TelegramLink;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanExpiredTelegramLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-telegram-links';

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
        TelegramLink::where('expires_at', '<', Carbon::now())->delete();
        $this->info('Просроченные токены удалены.');
    }
}
