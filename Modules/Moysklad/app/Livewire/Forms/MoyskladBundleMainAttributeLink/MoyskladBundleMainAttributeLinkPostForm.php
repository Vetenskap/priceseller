<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladBundleMainAttributeLink;

use App\Rules\ItemMainAttribute;
use App\Rules\Type;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;

class MoyskladBundleMainAttributeLinkPostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladBundleMainAttributeLink $moyskladBundleLink = null;
    public ?Collection $bundleAttributes = null;

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
    #[Validate]
    public $invert = false;

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function setBundleAttributes(Collection $bundleAttributes): void
    {
        $this->bundleAttributes = $bundleAttributes;
    }

    public function setMoyskladBundleLink(MoyskladBundleMainAttributeLink $moyskladBundleLink): void
    {
        $this->moyskladBundleLink = $moyskladBundleLink;
        $this->attribute_name = $moyskladBundleLink->attribute_name;
        $this->link = $moyskladBundleLink->link;
        $this->link_name = $moyskladBundleLink->link_name;
        $this->link_label = $moyskladBundleLink->link_label;
        $this->type = $moyskladBundleLink->type;
        $this->user_type = $moyskladBundleLink->user_type;
        $this->invert = $moyskladBundleLink->invert;
    }

    public function rules(): array
    {
        return [
            'attribute_name' => [
                'required',
                'string',
                new ItemMainAttribute,
                Rule::unique('moysklad_bundle_main_attribute_links', 'attribute_name')
                    ->where('moysklad_id', $this->moysklad->id)
                    ->when($this->moyskladBundleLink, fn(Unique $unique) => $unique->ignore($this->moyskladBundleLink->id, 'id')),
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
            'invert' => ['nullable', 'boolean'],
        ];
    }

    public function setOtherFields(): void
    {
        if ($this->bundleAttributes) {
            $bundleField = $this->bundleAttributes->where('name', $this->link)->first();
            if (isset($bundleField['type'])) {
                $this->link_label = $bundleField['label'];
                $this->link_name = $bundleField['type'] === 'metadata' ? $bundleField['label'] : $bundleField['name'];
                $this->type = $bundleField['type'];
            }
        }
    }

    public function store(): void
    {
        $this->setOtherFields();

        $this->validate();

        $this->moysklad->bundleMainAttributeLinks()->create($this->except(['moysklad', 'moyskladBundleLink']));

        $this->reset(['attribute_name', 'link', 'link_name', 'link_label', 'type', 'user_type', 'invert']);
    }

    public function update(): void
    {
        $this->setOtherFields();

        $this->validate();

        $this->moyskladBundleLink->update($this->except(['moysklad', 'moyskladBundleLink']));
    }

    public function destroy(): void
    {
        $this->moyskladBundleLink->delete();
    }
}
