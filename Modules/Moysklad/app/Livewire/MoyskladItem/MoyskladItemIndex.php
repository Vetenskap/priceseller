<?php

namespace Modules\Moysklad\Livewire\MoyskladItem;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Moysklad\Imports\MoyskladItemsImport;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemIndex extends Component
{
    use WithFileUploads;

    public Moysklad $moysklad;

    public Collection $assortmentAttributes;

    public $file;

    #[Session]
    public $code = null;
    #[Session]
    public $article = null;
    #[Session]
    public $brand = null;
    #[Session]
    public $name = null;
    #[Session]
    public $multiplicity = null;
    #[Session]
    public $unload_ozon = null;
    #[Session]
    public $unload_wb = null;

    public function import()
    {
        $attributes = collect($this->only(['code', 'article', 'brand', 'name', 'multiplicity', 'unload_ozon', 'unload_wb']));
        $attributes = $attributes->map(function ($value, $key) {
            $name = collect($this->assortmentAttributes->where('id', $value)->first())->get('name');
            return Str::slug(Str::isUuid($value) ? 'Доп. поле: ' . $name : $name, '_');
        });

        $import = new MoyskladItemsImport(\auth()->user()->id, $attributes, $this->moysklad);
        \Excel::import($import, $this->file);
        dd($import);
    }

    public function mount()
    {
        $service = new MoyskladService($this->moysklad);
        $this->assortmentAttributes = $service->getAllAssortmentAttributes();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-item.moysklad-item-index');
    }
}
