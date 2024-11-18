<div>
    <x-layouts.header :name="$supplier->name"/>
    <x-layouts.actions>
        @if($this->user()->can('delete-suppliers'))
                <flux:modal.trigger name="delete-supplier">
                    <flux:button variant="danger">Удалить</flux:button>
                </flux:modal.trigger>

                <flux:modal name="delete-supplier" class="min-w-[22rem] space-y-6">
                    <div>
                        <flux:heading size="lg">Удалить поставщика?</flux:heading>

                        <flux:subheading>
                            <p>Вы действительно хотите удалить этого поставщика?</p>
                            <p>Это действие нельзя будет отменить. Так же удалятся все связанные товары, их связи т.д.</p>
                        </flux:subheading>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer/>

                        <flux:modal.close>
                            <flux:button variant="ghost">Отменить</flux:button>
                        </flux:modal.close>

                        <flux:button wire:click="destroy" variant="danger">Удалить</flux:button>
                    </div>
                </flux:modal>
        @endif
    </x-layouts.actions>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general" icon="home">Основное</flux:tab>
                    <flux:tab name="warehouses" icon="truck">Склады</flux:tab>
                    <flux:tab name="price" icon="document-currency-dollar">Прайс</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:input wire:model.live="form.name" label="Наименование" required/>
                        <div class="max-w-fit">
                            <flux:switch wire:model.live="form.open" label="Включен" />
                        </div>
                        <div class="max-w-md">
                            <flux:switch wire:model.live="form.use_brand" label="Использовать бренд"
                            description="При установке этого параметра поиск товаров в базе будет производится с брендом"/>
                        </div>
                        <div class="max-w-md">
                            <flux:switch wire:model.live="form.unload_without_price" label="Выгружать без прайса"
                            description="При установке этого параметра поставщик больше не будет выгружаться с почты, будут использоваться резервная цена и остатки с ваших складов для выгрузки в кабинеты каждый час"/>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
                <livewire:supplier-report.supplier-report-index :supplier="$supplier"/>
            </flux:tab.panel>
            <flux:tab.panel name="warehouses">
                <livewire:supplier-warehouse.supplier-warehouse-index :supplier="$supplier" />
            </flux:tab.panel>
            <flux:tab.panel name="price">
                <x-blocks.main-block>
                    <flux:button wire:click="download">Скачать</flux:button>
                </x-blocks.main-block>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
    @if($this->user()->can('update-suppliers'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>
