<?php

namespace App\Livewire;

use App\Models\Module;
use Illuminate\Support\Collection;

class ModuleComponent extends BaseComponent
{
    public function changeOpen(array $module): void
    {
        $userModule = auth()->user()->modules()->where('module_id', $module['id'])->first();

        if ($userModule) {
            $userModule->enabled = !$userModule->enabled;
            $userModule->save();
        } else {
            auth()->user()->modules()->create([
                'module_id' => $module['id'],
                'enabled' => true
            ]);
        }

    }

    public function getEnabledModules(): Collection
    {
        $enabledModules = [];

        foreach (\Module::allEnabled() as $name => $module) {
            $enabledModules[] = $name;
        }

        return Module::whereIn('name', $enabledModules)->get();
    }

}