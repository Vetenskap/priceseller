<?php

namespace Modules\Moysklad\Livewire\MoyskladBundle;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\Moysklad\Jobs\MoyskladBundlesApiImport;
use Modules\Moysklad\Models\Moysklad;

class MoyskladBundleIndex extends Component
{
    use WithJsNotifications;

    public Moysklad $moysklad;

    public function importApi()
    {
        MoyskladBundlesApiImport::dispatch($this->moysklad);
        $this->addJobNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle.moysklad-bundle-index');
    }
}
