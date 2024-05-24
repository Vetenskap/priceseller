<?php

namespace App\Console\Commands\Import;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Console\Command;

class ImportPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:permissions';

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
        $permission = Permission::where('value', 'main_sub')->first();

        User::all()->each(function (User $user) use ($permission) {
             UserPermission::updateOrCreate([
                 'user_id' => $user->id,
                 'permission_id' => $permission->id
             ], [
                 'user_id' => $user->id,
                 'permission_id' => $permission->id,
                 'expires' => now()->addWeeks(2)->timestamp
             ]);
        });
    }
}
