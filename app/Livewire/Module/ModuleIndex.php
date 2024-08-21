<?php

namespace App\Livewire\Module;

use App\Livewire\BaseComponent;
use App\Models\Module;

class ModuleIndex extends BaseComponent
{
    public function changeOpen(array $module)
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

    public function render()
    {
        $enabledModules = [];

        foreach (\Module::allEnabled() as $name => $module) {
            $enabledModules[] = $name;
        }

        return view('livewire.module.module-index', [
            'modules' => Module::whereIn('name', $enabledModules)->get()
        ]);
    }
}
