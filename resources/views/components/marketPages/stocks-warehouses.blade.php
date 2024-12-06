@props(['market' => null])

<div>
    @if(!$market?->warehouses()->count())
        <x-blocks.main-block>
            <flux:card class="space-y-6">

                <x-blocks.center-block class="w-full bg-yellow-200 p-6 dark:text-yellow-200 rounded">
                    <flux:subheading>Ни один склад не добавлен. Остатки не будут выгружаться</flux:subheading>
                </x-blocks.center-block>
            </flux:card>
        </x-blocks.main-block>
    @endif
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <div class="flex">
                <flux:switch wire:model="form.enabled_stocks" label="Включить выгрузку остатков"/>
                <flux:switch wire:model="form.enabled_orders" label="Учитывать заказы"/>
            </div>
            <div class="flex">
                <div class="space-y-6">
                    <flux:input type="number" wire:model.live="form.minus_stock" label="Вычесть" />
                    <flux:heading size="lg">Ставить остаток 1 если</flux:heading>
                    <div class="flex gap-12">
                        <flux:input type="number" wire:model.live="form.min" label="Остаток от" />
                        <flux:input type="number" wire:model.live="form.max" label="Остаток до" />
                    </div>
                    <flux:input type="number" wire:model.live="form.max_count" label="Максимальный остаток" />
                </div>
            </div>
        </flux:card>
    </x-blocks.main-block>
</div>
