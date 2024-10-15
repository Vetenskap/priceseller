<x-blocks.main-block>

    <flux:card class="space-y-6">

        <flux:heading size="xl">{{$emailSupplier->supplier->name}}</flux:heading>

        <div x-data="{ open: false }">

            <flux:button @click="open = ! open">Редактировать</flux:button>

            <div x-show="open" class="mt-6 space-y-6">
                <div class="flex justify-between">
                    <flux:button wire:click="update">Сохранить</flux:button>
                    <flux:button variant="danger" wire:click="destroy">Удалить</flux:button>
                </div>

                <flux:card>
                    <flux:tab.group>
                        <flux:tabs>
                            <flux:tab name="general" icon="home">Основное</flux:tab>
                            <flux:tab name="warehouses" icon="truck">Склады</flux:tab>
                            <flux:tab name="stocks-values" icon="document">Значения остатков</flux:tab>
                        </flux:tabs>

                        <flux:tab.panel name="general">
                            <div class="flex gap-12">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="xl">Основная информация</flux:heading>
                                    </div>

                                    <flux:select variant="combobox" placeholder="Выберите поставщика..."
                                                 wire:model="form.supplier_id" label="Поставщик">

                                        @foreach(auth()->user()->suppliers as $supplier)
                                            <flux:option value="{{ $supplier->id }}">{{$supplier->name}}</flux:option>
                                        @endforeach
                                    </flux:select>

                                    <flux:input wire:model="form.email" label="Почта" required/>
                                    <flux:input wire:model="form.filename" label="Наименование файла" required/>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="xl">Информация по файлу</flux:heading>
                                    </div>

                                    <flux:input wire:model="form.header_article" label="Артикул" type="number" required/>
                                    <flux:input wire:model="form.header_price" label="Цена" type="number" required/>
                                    <flux:input wire:model="form.header_count" label="Остаток" type="number" required/>
                                    <flux:input wire:model="form.header_brand" label="Бренд" type="number"/>
                                    <flux:input wire:model="form.header_warehouse" label="Склад" type="number"/>
                                </div>
                            </div>
                        </flux:tab.panel>
                        <flux:tab.panel name="warehouses">
                            <livewire:email-supplier-warehouse.email-supplier-warehouse-index :email-supplier="$emailSupplier"/>
                        </flux:tab.panel>
                        <flux:tab.panel name="stocks-values">
                            <livewire:email-supplier-stock-value.email-supplier-stock-value-index :email-supplier="$emailSupplier"/>
                        </flux:tab.panel>
                    </flux:tab-group>
                </flux:card>
            </div>

        </div>
    </flux:card>
</x-blocks.main-block>
