<?php

namespace App\Livewire\BundlePlural;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithItemsFind;
use App\Models\Bundle;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;

class BundlePluralIndex extends BaseComponent
{
    use WithItemsFind;

    public Bundle $bundle;

    #[Validate]
    public $item_id;

    #[Validate]
    public $multiplicity;

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'uuid', 'exists:items,id', Rule::unique('bundle_items', 'item_id')->where('bundle_id', $this->bundle->id)],
            'multiplicity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->bundle);

        $this->validate();

        $this->bundle->items()->attach($this->only('item_id'), [
            'multiplicity' => $this->multiplicity,
            'updated_at' => now(),
            'created_at' => now(),
        ]);

    }

    public function destroy($id): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->bundle);

        $this->bundle->items()->detach($id);
    }


    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.bundle-plural.bundle-plural-index');
    }
}
