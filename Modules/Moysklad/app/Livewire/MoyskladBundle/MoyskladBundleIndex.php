<?php

namespace Modules\Moysklad\Livewire\MoyskladBundle;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Modules\Moysklad\Jobs\MoyskladBundlesApiImport;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;

class MoyskladBundleIndex extends BaseComponent
{
    use WithJsNotifications;

    public Moysklad $moysklad;

    public function importApi()
    {
        $status = $this->checkTtlJob(MoyskladBundlesApiImport::getUniqueId($this->moysklad), MoyskladBundlesApiImport::class);

        if ($status) MoyskladBundlesApiImport::dispatch($this->moysklad);
    }


    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle.moysklad-bundle-index');
    }
}
