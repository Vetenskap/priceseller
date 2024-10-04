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
                <div class="space-y-6">
                    <flux:input type="number" wire:model="form.minus_stock" label="Вычесть" />
                    <flux:heading size="lg">Ставить остаток 1 если</flux:heading>
                    <div class="flex gap-12">
                        <flux:input type="number" wire:model="form.min" label="Остаток от" />
                        <flux:input type="number" wire:model="form.max" label="Остаток до" />
                    </div>
                    <flux:input type="number" wire:model="form.max_count" label="Максимальный остаток" />
                </div>
            </div>
        </flux:card>
    </x-blocks.main-block>
</div>
