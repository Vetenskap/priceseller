@props(['market' => null])

<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl" class="text-center">Дополнительные поля</flux:heading>
            <flux:subheading class="text-center">Добавить в таблицу дополнительные поля</flux:subheading>
            <flux:card>
                <flux:checkbox.group wire:model.live="form.export_ext_item_fields" label="Поля товара">
                    @foreach(\App\Models\Item::MAINATTRIBUTES as $attribute)
                        <flux:checkbox :value="$attribute['name']" :label="$attribute['label']" />
                    @endforeach
                </flux:checkbox.group>
            </flux:card>
            <flux:error name="form.export_ext_item_fields" />
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <x-blocks.center-block>
                <flux:heading size="xl">Экспорт</flux:heading>
            </x-blocks.center-block>
            <x-blocks.center-block>
                <flux:button wire:click="export">Экспортировать</flux:button>
            </x-blocks.center-block>
            <livewire:items-export-report.items-export-report-index :model="$market"/>
        </flux:card>
    </x-blocks.main-block>
</div>
