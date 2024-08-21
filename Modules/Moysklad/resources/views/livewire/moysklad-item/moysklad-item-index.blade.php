<div>
    <livewire:moysklad::moysklad-item-main-attribute-link.moysklad-item-main-attribute-link-index :moysklad="$moysklad"/>
    <livewire:moysklad::moysklad-item-additional-attribute-link.moysklad-item-additional-attribute-link-index :moysklad="$moysklad"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Выгрузка товаров с Моего склада" />
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-success-button wire:click="importApi">Выгрузить по АПИ</x-success-button>
        </x-blocks.main-block>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Вебхуки" />
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Вебхук на создание товара"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'product'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addCreateWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Вебхук на изменение товара"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'product'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addUpdateWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Вебхук на удаление товара"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'DELETE', 'type' => 'product'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addDeleteWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
        <div wire:loading wire:target="import, deleteWebhook, addUpdateWebhook, addCreateWebhook, importApi, addDeleteWebhook">
            <x-loader/>
        </div>
    </x-layouts.main-container>
</div>
