<?php

namespace App\Livewire\Moysklad;

use App\Livewire\Forms\MoyskladPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use App\Services\MoyskladService;
use Illuminate\Support\Str;
use Livewire\Component;

class MoyskladIndex extends Component
{
    use WithJsNotifications;

    public MoyskladPostForm $form;

    public $selectedTab;

    public $apiWarehouses;

    public $suppliers;
    public $selectedSupplier;
    public $unloadOrders = false;

    public function mount()
    {
        $this->form->setMoysklad(auth()->user()->moysklad);
        if ($this->form->moysklad) {
            $service = new MoyskladService($this->form->moysklad);
            $service->setClient();

            $this->apiWarehouses = $service->getWarehouses();

            $this->suppliers = $service->getSuppliers();
            $this->selectedSupplier = $this->suppliers->first()['id'];
        }
    }

    public function save()
    {
        if ($this->form->moysklad) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        $this->addSuccessSaveNotification();
    }

    public function addSupplier()
    {
        $name = $this->suppliers->firstWhere('id', $this->selectedSupplier)['name'];

        $this->authorize('create', Supplier::class);

        auth()->user()->suppliers()->updateOrCreate([
            'ms_uuid' => $this->selectedSupplier
        ], [
            'ms_uuid' => $this->selectedSupplier,
            'name' => $name
        ]);

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        return view('livewire.moysklad.moysklad-index');
    }
}
