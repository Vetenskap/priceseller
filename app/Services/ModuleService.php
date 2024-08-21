<?php

namespace App\Services;

use App\Models\Module;
use App\Models\User;

class ModuleService
{
    public static function moduleIsEnabled(string $name, User $user): bool
    {
        if (\Module::find($name)->isEnabled()) {

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
}
