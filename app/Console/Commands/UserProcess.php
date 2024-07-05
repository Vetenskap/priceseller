<?php

namespace App\Console\Commands;

use App\Jobs\Email\CheckEmails;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class UserProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'unload users prices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::chunk(10, function (Collection $users) {
            $users->each(function (User $user) {
                if (App::isLocal()) {
                    if ($user->isAdmin()) {
                        CheckEmails::dispatch($user->id);
                    }
                } else {
                    CheckEmails::dispatch($user->id);
                }
            });
        });
    }
}
