<x-layouts.ozon-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-blocks.flex-block>
        <x-inputs.switcher :disabled="$market->close" :checked="$form->open" wire:model="form.open"/>
        <x-layouts.simple-text name="Включить"/>
    </x-blocks.flex-block>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="name"
                                   type="text"
                                   field="form.name"
        >Наименование
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="client_id"
                                   type="text"
                                   field="form.client_id"
        >Идентификатор клиента
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="api_key"
                                   type="text"
                                   field="form.api_key"
        >АПИ ключ
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
    <x-blocks.flex-block-end>
        <x-dropdowns.dropdown-select name="organization_id"
                                     field="form.organization_id"
                                     :current-id="$form->organization_id"
                                     :items="auth()->user()->organizations">
            Организация
        </x-dropdowns.dropdown-select>
    </x-blocks.flex-block-end>
</x-layouts.ozon-market-edit-layout>
