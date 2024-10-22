<div>
    <flux:card class="space-y-6">

        <flux:heading size="xl">{{$supplier->supplier->name}}</flux:heading>

        <div x-data="{ open: false }">

            <flux:button @click="open = ! open">Редактировать</flux:button>

            <div x-show="open" class="mt-6 space-y-6">
                @if($this->user()->can('update-wb'))
                    <flux:button
                        variant="danger"
                        wire:click="destroy"
                        wire:confirm="Вы действительно хотите удалить этого поставщика?"
                    >Удалить</flux:button>
                @endif

                <livewire:wb-warehouse-supplier-warehouse.wb-warehouse-supplier-warehouse-index
                    :supplier="$supplier"/>
            </div>
        </div>
    </flux:card>
</div>
