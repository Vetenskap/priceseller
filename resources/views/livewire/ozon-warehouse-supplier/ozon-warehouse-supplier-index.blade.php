<div>
    <flux:card class="space-y-6">
        @if($this->user()->can('update-ozon'))
            <flux:input.group>
                <flux:select variant="combobox" placeholder="Выберите поставщика..." wire:model="supplier_id">

                    @foreach($warehouse->market->suppliers() as $supplier)
                        <flux:option :value="$supplier->id">{{$supplier->name}}</flux:option>
                    @endforeach
                </flux:select>

                <flux:button icon="plus" wire:click="store">Добавить</flux:button>
            </flux:input.group>
        @endif
        <flux:heading size="xl">Список</flux:heading>
        @foreach($warehouse->suppliers as $supplier)
            <livewire:ozon-warehouse-supplier.ozon-warehouse-supplier-edit :supplier="$supplier" :key="$supplier->getKey()"/>
        @endforeach
    </flux:card>
</div>
