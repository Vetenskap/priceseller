<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladItemAdditionalAttributeLink;

use App\Rules\Type;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemAdditionalAttributeLink;

class MoyskladItemAdditionalAttributeLinkPostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladItemAdditionalAttributeLink $moyskladItemLink = null;
    public Collection $assortmentAttributes;

    #[Validate]
    public $item_attribute_id;
    #[Validate]
    public $link;
    #[Validate]
    public $link_name;
    #[Validate]
    public $link_label;
    #[Validate]
    public $type;
    #[Validate]
    public $user_type;
    #[Validate]
    public $invert = false;

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function setAssortmentAttributes(Collection $assortmentAttributes): void
    {
        $this->assortmentAttributes = $assortmentAttributes;
    }

    public function setMoyskladItemLink(MoyskladItemAdditionalAttributeLink $moyskladItemLink): void
    {
        $this->moyskladItemLink = $moyskladItemLink;
        $this->item_attribute_id = $moyskladItemLink->item_attribute_id;
        $this->link = $moyskladItemLink->link;
        $this->link_name = $moyskladItemLink->link_name;
        $this->link_label = $moyskladItemLink->link_label;
        $this->type = $moyskladItemLink->type;
        $this->user_type = $moyskladItemLink->user_type;
        $this->invert = $moyskladItemLink->invert;
    }

    public function rules(): array
    {
        return [
            'item_attribute_id' => [
                'required',
                'string',
                Rule::unique('moysklad_item_additional_attribute_links', 'item_attribute_id')
                    ->when($this->moyskladItemLink, fn(Unique $unique) => $unique->ignore($this->moyskladItemLink->id, 'id')),
            ],
            'link' => ['required', 'string'],
            'link_name' => ['required', 'string'],
            'link_label' => ['required', 'string'],
            'type' => ['required', 'string'],
            'user_type' => [
                'required',
                'string',
                new Type
            ],
            'invert' => ['nullable', 'boolean']
        ];
    }

    public function setOtherFields(): void
    {
        $assortmentField = $this->assortmentAttributes->where('name', $this->link)->first();
        if (isset($assortmentField['type'])) {
            $this->link_label = $assortmentField['label'];
            $this->link_name = $assortmentField['type'] === 'metadata' ? $assortmentField['label'] : $assortmentField['name'];
            $this->type = $assortmentField['type'];
        }
    }

    public function store(): void
    {
        $this->setOtherFields();

        $this->validate();

        $this->moysklad->itemAdditionalAttributeLinks()->create($this->except(['moysklad', 'moyskladItemLink']));

        $this->reset(['item_attribute_id', 'link', 'link_name', 'link_label', 'type', 'user_type', 'invert']);
    }

    public function update(): void
    {
        $this->setOtherFields();

        $this->validate();

        $this->moyskladItemLink->update($this->except(['moysklad', 'moyskladItemLink']));
    }

    public function destroy(): void
    {
        $this->moyskladItemLink->delete();
    }
}
