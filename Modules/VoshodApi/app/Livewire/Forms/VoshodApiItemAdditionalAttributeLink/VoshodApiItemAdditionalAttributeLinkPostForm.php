<?php

namespace VoshodApiItemAdditionalAttributeLink;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Models\VoshodApiItemAdditionalAttributeLink;

class VoshodApiItemAdditionalAttributeLinkPostForm extends Form
{
    public VoshodApi $voshodApi;

    public ?VoshodApiItemAdditionalAttributeLink $voshodItemLink = null;

    #[Validate]
    public $link;
    #[Validate]
    public $item_attribute_id;

    public function setVoshodApi(VoshodApi $voshodApi): void
    {
        $this->voshodApi = $voshodApi;
    }

    public function setVoshodItemLink(VoshodApiItemAdditionalAttributeLink $voshodItemLink): void
    {
        $this->voshodItemLink = $voshodItemLink;
    }

    public function rules(): array
    {
        return [
            'link' => ['required', 'string'],
            'item_attribute_id' => ['required', 'uuid', 'exists:item_attributes,id'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->voshodApi->itemAdditionalAttributeLinks()->create($this->except(['voshodApi', 'voshodItemLink']));

        $this->reset(['link', 'item_attribute_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->voshodItemLink->update($this->except(['voshodApi', 'voshodItemLink']));
    }

    public function destroy(): void
    {
        $this->voshodItemLink->delete();
    }
}
