<div>
    <x-layouts.header :name="$organization->name" />
    <x-layouts.actions>
        <flux:button wire:click="update">Сохранить</flux:button>
        <flux:button variant="danger" wire:click="destroy">Удалить</flux:button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:input wire:model="form.name" label="Наименование" required/>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
