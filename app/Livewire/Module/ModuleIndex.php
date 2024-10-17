<?php

namespace App\Livewire\Module;

use App\Livewire\BaseComponent;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ModuleIndex extends BaseComponent
{
    public function changeOpen(array $module): void
    {
        $userModule = $this->currentUser()->modules()->where('module_id', $module['id'])->first();

        if (!ModuleService::moduleIsVisible($module['name'], $this->currentUser())) {
            return;
        }

        if ($userModule) {
            $userModule->enabled = !$userModule->enabled;
            $userModule->save();
        } else {
            $this->currentUser()->modules()->create([
                'module_id' => $module['id'],
                'enabled' => true
            ]);
        }

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
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
