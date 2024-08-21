<?php

namespace SamsonApiItemAdditionalAttributeLink;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Models\SamsonApiItemAdditionalAttributeLink;

class SamsonApiItemAdditionalAttributeLinkPostForm extends Form
{
    public SamsonApi $samsonApi;

    public ?SamsonApiItemAdditionalAttributeLink $samsonItemLink = null;

    #[Validate]
    public $link;
    #[Validate]
    public $item_attribute_id;

    public function setSamsonApi(SamsonApi $samsonApi): void
    {
        $this->samsonApi = $samsonApi;
    }

    public function setSamsonItemLink(SamsonApiItemAdditionalAttributeLink $samsonItemLink): void
    {
        $this->samsonItemLink = $samsonItemLink;
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

        $this->samsonApi->itemAdditionalAttributeLinks()->create($this->except(['samsonApi', 'samsonItemLink']));

        $this->reset(['link', 'item_attribute_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->samsonItemLink->update($this->except(['samsonApi', 'samsonItemLink']));
    }

    public function destroy(): void
    {
        $this->samsonItemLink->delete();
    }
}
