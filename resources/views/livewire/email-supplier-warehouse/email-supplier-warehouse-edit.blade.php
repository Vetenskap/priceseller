<div>
    <div class="flex gap-6">
        <flux:input wire:model="form.value" label="Название в прайсе" required/>
        <flux:select variant="combobox" placeholder="Выберите склад..."
                     wire:model="form.supplier_warehouse_id" label="Склад">

            @foreach($emailSupplier->supplier->warehouses as $warehouse)
                <flux:option value="{{ $warehouse->id }}">{{$warehouse->name}}</flux:option>
            @endforeach
        </flux:select>
        <div class="self-end">
            <flux:button variant="danger" wire:click="destroy">Удалить</flux:button>
        </div>
    </div>
</div>
