<div>
    <x-layouts.header :name="$warehouse->name"/>
    <x-layouts.actions>
        <flux:button wire:click="update">Сохранить</flux:button>
        <flux:button variant="danger" wire:click="destroy">Удалить</flux:button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general">Основная информация</flux:tab>
                    <flux:tab name="stocks">Остатки</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:input wire:model="form.name" label="Наименование" required/>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks">
                <livewire:warehouse-item-stock.warehouse-item-stock-index :warehouse="$warehouse" />
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
</div>
