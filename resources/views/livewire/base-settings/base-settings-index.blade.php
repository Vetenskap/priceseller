<div>
    <x-layouts.header name="Общие настройки"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:button wire:click="save">Сохранить</flux:button>
                <flux:switch wire:model="enabled_use_buy_price_reserve" label="Использовать резервную закупочную цену"/>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
