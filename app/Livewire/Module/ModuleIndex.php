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
    public $changeOpen = [];

    public function mount()
    {
        $this->changeOpen = $this->currentUser()->modules->mapWithKeys(fn (UserModule $userModule) => [$userModule->module_id => (bool) $userModule->enabled])->toArray();
    }

    public function updatedChangeOpen(): void
    {
        foreach ($this->changeOpen as $key => $value) {
            $userModule = $this->currentUser()->modules()->where('module_id', $key)->first();

            if ($userModule) {
                $userModule->enabled = $value;
                $userModule->save();
            } else {
                $this->currentUser()->modules()->create([
                    'module_id' => $key,
                    'enabled' => true
                ]);
            }
        }

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $enabledModules = [];

        foreach (\Module::allEnabled() as $name => $module) {
            if (Permission::where('value', Str::lower($name))->exists()) {
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
