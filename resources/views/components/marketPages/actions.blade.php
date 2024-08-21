<div>
    <x-blocks.main-block>
        <x-layouts.title name="Действия" />
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-secondary-button wire:click="testPrice" wire:confirm="Вы действительно хотите пересчитать цены? Действие происходит в реальном времени, не перезагружайте страницу.">Пересчитать цены</x-secondary-button>
        <x-secondary-button wire:click="nullStocks" wire:confirm="Вы действительно хотите занулить кабинет? Действие нельзя будет отменить.">Занулить кабинет</x-secondary-button>
    </x-blocks.flex-block>
</div>
