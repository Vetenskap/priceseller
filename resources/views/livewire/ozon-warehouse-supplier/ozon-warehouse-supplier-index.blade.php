<div>
    <flux:card class="space-y-6">
        <flux:input.group>
            <flux:select variant="listbox" searchable placeholder="Выберите поставщика..." wire:model="supplier_id">
                <x-slot name="search">
                    <flux:select.search placeholder="Поиск..." />
                </x-slot>

                @foreach(auth()->user()->suppliers as $supplier)
                    <flux:option :value="$supplier->id">{{$supplier->name}}</flux:option>
                @endforeach
            </flux:select>

            <flux:button icon="plus" wire:click="store">Добавить</flux:button>
        </flux:input.group>
        <flux:heading size="xl">Список</flux:heading>
        @foreach($warehouse->suppliers as $supplier)
            <livewire:ozon-warehouse-supplier.ozon-warehouse-supplier-edit :supplier="$supplier" :key="$supplier->getKey()"/>
        @endforeach
    </flux:card>
</div>
