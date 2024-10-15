<?php

namespace App\Livewire\Forms\Bundle;

use App\Models\Bundle;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BundlePostForm extends Form
{
    public ?Bundle $bundle = null;

    #[Validate]
    public $ms_uuid = null;

    #[Validate]
    public $code;

    #[Validate]
    public $name = null;

    public function rules(): array
    {
        return [
            'ms_uuid' => [
                'nullable',
                'uuid',
                Rule::unique('bundles', 'code')
                    ->when($this->bundle, fn (Unique $unique) => $unique->ignore($this->bundle->getKey(), 'id')),
            ],
            'code' => [
                'required',
                'string',
                Rule::unique('bundles', 'code')
                    ->where('user_id', auth()->user()->id)
                    ->when($this->bundle, fn (Unique $unique) => $unique->ignore($this->bundle->getKey(), 'id')),
            ],
            'name' => ['nullable', 'string']
        ];
    }

    public function setBundle(Bundle $bundle): void
    {
        $this->bundle = $bundle;
        $this->ms_uuid = $bundle->ms_uuid;
        $this->code = $bundle->code;
        $this->name = $bundle->name;
    }

    public function store(): void
    {
        $this->validate();

        auth()->user()->bundles()->create($this->except('bundle'));
    }

    public function update(): void
    {
        $this->validate();

        $this->bundle->update($this->except('bundle'));
    }

    public function destroy(): void
    {
        $this->bundle->delete();
    }
}
