<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <div class="flex">
                <flux:switch wire:model.live="enabled_orders" label="Учитывать заказы при выгрузке"/>
            </div>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Управление</flux:heading>
            <flux:button variant="danger">Очистить все заказы</flux:button>
            <flux:heading size="lg">Автоматическое очищение</flux:heading>
            <flux:subheading>Вы можете добавить срок жизни заказа, после его истечения заказ не будет учитываться
            </flux:subheading>
            <flux:input type="number" label="Время в минутах" wire:model.live="clear_order_time"/>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Вебхук</flux:heading>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'customerorder'])->first())
                <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
            @else
                <flux:button wire:click="addWebhook">Добавить</flux:button>
            @endif
        </flux:card>
    </x-blocks.main-block>
    {!! $this->renderSaveButton() !!}
</div>
