<?php

namespace Modules\Moysklad\Livewire\MoyskladRecountRetailMarkup;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
use Modules\Moysklad\Livewire\Forms\MoyskladRecountRetailMarkup\MoyskladRecountRetailMarkupForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladRecountRetailMarkup;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladRecountRetailMarkupIndex extends Component
{
    public MoyskladRecountRetailMarkupForm $form;
    public Moysklad $moysklad;
    public Collection $assortmentAttributes;
    public Collection $priceTypes;

    public $dirtyRecountRetailMarkup;

    public function updatedDirtyRecountRetailMarkup(): void
    {
        foreach ($this->dirtyRecountRetailMarkup as $id => $values) {
            $recountRetailMarkup = MoyskladRecountRetailMarkup::find($id);
            $recountRetailMarkup->update($values);
        }
    }

    public function store(): void
    {
        $this->form->store();
        \Flux::modal('create')->close();
    }

    public function destroy($id): void
    {
        $recountRetailMarkup = MoyskladRecountRetailMarkup::find($id);
        $recountRetailMarkup->delete();
    }

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
        $this->assortmentAttributes = (new MoyskladService($this->form->moysklad))->getAllAssortmentAttributes();
        $this->form->setAssortmentAttributes($this->assortmentAttributes);
        $this->priceTypes = PriceType::fetchAll($this->moysklad->api_key)->map(function (PriceType $priceType) {
            return ['name' => $priceType->id, 'label' => $priceType->getName()];
        });
        $this->dirtyRecountRetailMarkup = $this->moysklad->recountRetailMarkups->mapWithKeys(function (MoyskladRecountRetailMarkup $recountRetailMarkup) {
            return [
                $recountRetailMarkup->id => [
                    'enabled' => boolval($recountRetailMarkup->enabled),
                ]];
        });
    }

    public function render(): Factory|View|Application|\Illuminate\View\View
    {
        return view('moysklad::livewire.moysklad-recount-retail-markup.moysklad-recount-retail-markup-index');
    }
}
