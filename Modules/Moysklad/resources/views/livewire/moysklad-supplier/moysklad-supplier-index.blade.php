<div>
    <flux:modal name="create-moysklad-supplier" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Связать нового поставщика</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите поставщика..." label="Ваш поставщик (priceseller)"
                     wire:model="form.supplier_id">

            @foreach(auth()->user()->suppliers as $supplier)
                <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите поставщика..." label="Ваш поставщик (Мой склад)"
                     wire:model="form.moysklad_supplier_uuid">

            @foreach($moyskladSuppliers as $moyskladSupplier)
                <flux:option :value="$moyskladSupplier['id']">{{$moyskladSupplier['name']}}</flux:option>
            @endforeach
        </flux:select>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Связать</flux:button>
        </div>
    </flux:modal>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Связать нового поставщика</flux:heading>
            <flux:subheading>Вы можете привязать своих поставщиков с Моего Склада к своим существующим.</flux:subheading>
            <div>
                <flux:modal.trigger name="create-moysklad-supplier">
                    <flux:button>Связать</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Список</flux:heading>
            @if($this->suppliers->isNotEmpty())
                <flux:table :paginate="$this->suppliers">
                    <flux:columns>
                        <flux:column>Поставщик priceseller</flux:column>
                        <flux:column>Поставщик мой склад</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->suppliers as $supplier)
                            <flux:row :key="$supplier->getKey()">
                                <flux:cell>{{collect($moyskladSuppliers)->firstWhere('id', $supplier->moysklad_supplier_uuid)['name']}}</flux:cell>
                                <flux:cell>{{$supplier->supplier->name}}</flux:cell>
                                <flux:cell align="right">
                                    <flux:button icon="trash"
                                                 variant="danger"
                                                 size="sm"
                                                 wire:click="destroy({{ json_encode($supplier->getKey()) }})"
                                                 wire:target="destroy({{ json_encode($supplier->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить этого поставщика?"
                                    />
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>

