<div>
    <x-layouts.header name="Общие настройки"/>
    <x-layouts.main-container>
        <x-layouts.actions>
            <x-success-button wire:click="save">Сохранить</x-success-button>
        </x-layouts.actions>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$enabled_use_buy_price_reserve" wire:model="enabled_use_buy_price_reserve"/>
            <x-layouts.simple-text name="Использовать резервную закупочную цену" />
        </x-blocks.flex-block>
    </x-layouts.main-container>
    <div wire:loading wire:target="save">
        <x-loader />
    </div>
</div>
