<div>
    <flux:card class="space-y-6">

        <flux:heading size="xl">{{$supplier->supplier->name}}</flux:heading>

        <div x-data="{ open: false }">

            <flux:button @click="open = ! open">Редактировать</flux:button>

            <div x-show="open" class="mt-6 space-y-6">
                <flux:button variant="danger" wire:click="destroy">Удалить</flux:button>

                <livewire:wb-warehouse-supplier-warehouse.wb-warehouse-supplier-warehouse-index
                    :supplier="$supplier"/>
            </div>
        </div>
    </flux:card>
</div>
