<?php

namespace App\Livewire\Module;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Permission;
use App\Models\UserModule;
use App\Services\ModuleService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class ModuleIndex extends BaseComponent
{
    public function changeOpen(array $module): void
    {
        /** @var UserModule $userModule */
        $userModule = $this->currentUser()->modules()->where('module_id', $module['id'])->first();

        if (Permission::where('value', Str::lower($module['name']))->exists()) {
            if (!$this->user()->can('update-' . Str::lower($module['name']))) {
                abort(403);
            }
        }

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
            if (Permission::where('value', Str::lower($module['name']))->exists()) {
                if ($this->user() instanceof Employee) {
                    if (!$this->user()->can('view-' . $name)) continue;
                }
            }

            $enabledModules[] = $name;
        }

        return view('livewire.module.module-index', [
            'modules' => Module::whereIn('name', $enabledModules)->get()
        ]);
    }
}
