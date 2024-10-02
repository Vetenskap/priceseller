<div>
    <flux:modal.trigger name="create-item-attribute">
        <flux:button>Добавить новое поле</flux:button>
    </flux:modal.trigger>

    <flux:modal name="create-item-attribute" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание поля</flux:heading>
            <flux:subheading>Заполните все поля.</flux:subheading>
        </div>

        <flux:input label="Наименование" placeholder="Введите наименование поля" wire:model="name"/>

        <flux:select variant="listbox" searchable label="Тип" wire:model="type">
            <x-slot name="search">
                <flux:select.search placeholder="Поиск..."/>
            </x-slot>

            @foreach(config('app.item_attribute_types') as $type)
                <flux:option value="{{ $type['name'] }}">{{ $type['label'] }}</flux:option>
            @endforeach
        </flux:select>

        <div class="flex">
            <flux:spacer/>

            <flux:button wire:click="submit" variant="primary">Создать</flux:button>
        </div>
    </flux:modal>
</div>
