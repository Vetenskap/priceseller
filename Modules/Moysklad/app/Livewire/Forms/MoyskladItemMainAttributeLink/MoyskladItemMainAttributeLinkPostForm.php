<?php

namespace MoyskladItemMainAttributeLink;

use App\Rules\ItemMainAttribute;
use App\Rules\Type;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;

class MoyskladItemMainAttributeLinkPostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladItemMainAttributeLink $moyskladItemLink = null;
    public Collection $assortmentAttributes;

    #[Validate]
    public $attribute_name;
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

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function setAssortmentAttributes(Collection $assortmentAttributes): void
    {
        $this->assortmentAttributes = $assortmentAttributes;
    }

    public function setMoyskladItemLink(MoyskladItemMainAttributeLink $moyskladItemLink): void
    {
        $this->moyskladItemLink = $moyskladItemLink;
        $this->attribute_name = $moyskladItemLink->attribute_name;
        $this->link = $moyskladItemLink->link;
        $this->link_name = $moyskladItemLink->link_name;
        $this->link_label = $moyskladItemLink->link_label;
        $this->type = $moyskladItemLink->type;
        $this->user_type = $moyskladItemLink->user_type;
    }

    public function rules(): array
    {
        return [
            'attribute_name' => [
                'required',
                'string',
                new ItemMainAttribute,
                Rule::unique('moysklad_item_main_attribute_links', 'attribute_name')
                    ->where('moysklad_id', $this->moysklad->id)
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

        $this->moysklad->itemMainAttributeLinks()->create($this->except(['moysklad', 'moyskladItemLink']));

        $this->reset(['attribute_name', 'link', 'link_name', 'link_label', 'type', 'user_type']);
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
