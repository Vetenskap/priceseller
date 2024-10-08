<?php

namespace App\Services;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\App;

class ModuleService
{
    public static function moduleIsEnabled(string $name, User $user): bool
    {
        if (static::moduleIsVisible($name, $user)) {

            if ($module = Module::where('name', $name)->first()) {
                if ($userModule = $user->modules()->where('module_id', $module->id)->first()) {
                    if ($userModule->enabled) {
                        return true;
                    }
                }
            }

        }

        return false;
    }

    public static function moduleIsVisible(string $name, User $user): bool
    {
        if (\Module::find($name)?->isEnabled() && ($user->isSub() || $user->isAdmin() || App::isLocal())) {
            return true;
        }

        return false;
    }
}
