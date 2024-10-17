<div>
    <flux:card class="space-y-6">

        <flux:heading size="xl">{{$name}}</flux:heading>

        <div x-data="{ open: false }">

            <flux:button @click="open = ! open">Редактировать</flux:button>

            <div x-show="open" class="mt-6 space-y-6">
                <div class="flex justify-between">
                    <flux:button wire:click="update">Сохранить</flux:button>
                    <flux:button
                        variant="danger"
                        wire:click="destroy"
                        wire:confirm="Вы действительно хотите удалить этот склад?"
                    >Удалить</flux:button>
                </div>

                <flux:card>
                    <flux:tab.group>
                        <flux:tabs>
                            <flux:tab name="general" icon="home">Основное</flux:tab>
                            <flux:tab name="suppliers" icon="truck">Поставщики</flux:tab>
                            <flux:tab name="warehouses" icon="archive-box">Мои склады</flux:tab>
                        </flux:tabs>

                        <div class="mt-6">
                            @if(!$warehouse->suppliers()->count())
                                <flux:card>
                                    <x-blocks.center-block class="w-full bg-yellow-200 p-6 dark:bg-yellow-200 rounded">
                                        <flux:subheading>Ни один поставщик не добавлен. Остатки не будут выгружаться
                                        </flux:subheading>
                                    </x-blocks.center-block>
                                </flux:card>
                            @endif
                        </div>

                        <flux:tab.panel name="general">
                            <flux:card>
                                <div class="flex gap-12">
                                    <flux:input wire:model="name" label="Наименование" required/>
                                    <flux:input wire:model="warehouse_id" label="Идентификатор" readonly/>
                                </div>
                            </flux:card>
                        </flux:tab.panel>
                        <flux:tab.panel name="suppliers">
                            <livewire:ozon-warehouse-supplier.ozon-warehouse-supplier-index :warehouse="$warehouse" lazy/>
                        </flux:tab.panel>
                        <flux:tab.panel name="warehouses">
                            <livewire:ozon-warehouse-user-warehouse.ozon-warehouse-user-warehouse-index
                                :warehouse="$warehouse"/>
                        </flux:tab.panel>
                    </flux:tab.group>
                </flux:card>

            </div>
        </div>

    </flux:card>
</div>
