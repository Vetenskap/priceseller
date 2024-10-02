<div>
    <flux:card class="space-y-6">
        <flux:heading size="xl">Склады</flux:heading>
        <flux:subheading>Вы можете привязать склады с прайса к складам поставщика</flux:subheading>

        <flux:modal name="create-email-supplier-warehouse-{{$emailSupplier->getKey()}}" class="md:w-96 space-y-6">
            <div>
                <flux:heading size="lg">Добавление склада</flux:heading>
            </div>

            <flux:input wire:model="form.value" label="Название в прайсе" required/>
            <flux:select variant="listbox" searchable placeholder="Выберите склад..."
                         wire:model="form.supplier_warehouse_id" label="Склад">
                <x-slot name="search">
                    <flux:select.search placeholder="Поиск..."/>
                </x-slot>

                @foreach($emailSupplier->supplier->warehouses as $warehouse)
                    <flux:option value="{{ $warehouse->id }}">{{$warehouse->name}}</flux:option>
                @endforeach
            </flux:select>

            <div class="flex">
                <flux:spacer/>

                <flux:button variant="primary" wire:click="store">Создать</flux:button>
            </div>
        </flux:modal>

        <div>
            <flux:modal.trigger name="create-email-supplier-warehouse-{{$emailSupplier->getKey()}}">
                <flux:button>Добавить</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Все склады</flux:heading>
            @if($emailSupplier->warehouses->isNotEmpty())
                <flux:button wire:click="update">Сохранить</flux:button>
                @foreach($emailSupplier->warehouses as $warehouse)
                    <livewire:email-supplier-warehouse.email-supplier-warehouse-edit
                        :email-supplier-warehouse="$warehouse"
                        :email-supplier="$emailSupplier"
                        wire:key="{{$warehouse->getKey()}}"/>
                @endforeach
            @else
                <flux:subheading>Вы пока ещё не добавляли склады</flux:subheading>
            @endif
        </flux:card>

    </flux:card>
</div>
