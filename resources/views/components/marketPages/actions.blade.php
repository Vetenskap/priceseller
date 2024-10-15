<div>
    <x-blocks.main-block>
        <flux:card>
            <div class="flex gap-6">
                <flux:button wire:click="testPrice" wire:confirm="Вы действительно хотите пересчитать цены? Действие происходит в реальном времени, не перезагружайте страницу.">Пересчитать цены</flux:button>
                <flux:button wire:click="testStocks" wire:confirm="Вы действительно хотите пересчитать остатки? Действие происходит в реальном времени, не перезагружайте страницу.">Пересчитать остатки</flux:button>
                <flux:button wire:click="nullStocks" wire:confirm="Вы действительно хотите занулить кабинет? Действие нельзя будет отменить.">Занулить кабинет</flux:button>
            </div>
        </flux:card>
    </x-blocks.main-block>
</div>
