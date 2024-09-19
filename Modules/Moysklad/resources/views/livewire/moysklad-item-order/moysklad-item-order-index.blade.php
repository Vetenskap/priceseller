<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Основное" />
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-success-button wire:click="save">Сохранить</x-success-button>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$enabled_orders" wire:model="enabled_orders" />
            <x-layouts.simple-text name="Учитывать заказы при выгрузке" />
        </x-blocks.flex-block>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Управление" />
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-danger-button wire:click="clear">Очистить все заказы</x-danger-button>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-layouts.title name="Автоматическое очищение" />
            <x-information>Вы можете добавить срок жизни заказа, после его истечения заказ не будет учитываться</x-information>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-inputs.input-with-label name="clear_order_time"
                                       field="clear_order_time"
                                       type="number"
            >Время в минутах</x-inputs.input-with-label>
        </x-blocks.main-block>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Вебхук" />
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'customerorder'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
