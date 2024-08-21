@props(['form', 'market', 'page'])

<div>
    <x-layouts.header :name="$form->name"/>
    @error('error')
    <x-notify-top>
        <div class="bg-red-400 w-full p-2">
            <x-layouts.simple-text :name="$message"/>
        </div>
    </x-notify-top>
    @enderror
    <x-layouts.actions>
        <x-primary-button wire:click="back">Закрыть</x-primary-button>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy"
                         wire:confirm="Вы действительно хотите удалить кабинет? Все связи так же будут удалены.">Удалить
        </x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-marketPages.index route="ozon-market-edit" :market="$market" :page="$page"/>
        {{$slot}}
    </x-layouts.main-container>
</div>
