<div>
    <x-layouts.header :name="$supplier->name"/>
    <x-layouts.actions>
        <x-primary-button wire:click="back">Закрыть</x-primary-button>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy"
                         wire:confirm="Вы действительно хотите удалить поставщика? Так же будут удалены все связанные с ним товары.">
            Удалить
        </x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('supplier.edit', ['supplier' => $supplier->id, 'page' => 'main'])}}"
                              name="Основное" :active="$page === 'main'"/>
            <x-links.tab-link href="{{route('supplier.edit', ['supplier' => $supplier->id, 'page' => 'price'])}}"
                              name="Прайс" :active="$page === 'price'"/>
        </x-navigate-pages>
        <x-blocks.main-block>
            <x-layouts.title name="Основное" />
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$supplier->open" wire:model="form.open"/>
            <x-layouts.simple-text name="Включен" />
        </x-blocks.flex-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="form.name"
            >Наименование
            </x-inputs.input-with-label>
        </x-blocks.flex-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$supplier->use_brand" wire:model="form.use_brand"/>
            <x-layouts.simple-text name="Использовать бренд"/>
        </x-blocks.flex-block>
        <livewire:supplier-report.supplier-report-index :supplier="$supplier"/>
    </x-layouts.main-container>
    <div wire:loading wire:target="destroy">
        <x-loader/>
    </div>
</div>