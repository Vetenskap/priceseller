<?php

namespace BergApiItemAdditionalAttributeLink;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\BergApi\Models\BergApi;
use Modules\BergApi\Models\BergApiItemAdditionalAttributeLink;

class BergApiItemAdditionalAttributeLinkPostForm extends Form
{
    public BergApi $bergApi;

    public ?BergApiItemAdditionalAttributeLink $bergItemLink = null;

    #[Validate]
    public $link;
    #[Validate]
    public $item_attribute_id;

    public function setBergApi(BergApi $bergApi): void
    {
        $this->bergApi = $bergApi;
    }

    public function setVoshodItemLink(BergApiItemAdditionalAttributeLink $bergItemLink): void
    {
        $this->bergItemLink = $bergItemLink;
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

        $this->bergApi->itemAdditionalAttributeLinks()->create($this->except(['bergApi', 'bergItemLink']));

        $this->reset(['link', 'item_attribute_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->bergItemLink->update($this->except(['bergApi', 'bergItemLink']));
    }

    public function destroy(): void
    {
        $this->bergItemLink->delete();
    }
}
