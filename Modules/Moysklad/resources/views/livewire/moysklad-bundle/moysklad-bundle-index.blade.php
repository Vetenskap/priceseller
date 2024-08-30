<div>
    <livewire:moysklad::moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-index :moysklad="$moysklad"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Выгрузка комплектов с Моего склада" />
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
            <x-titles.sub-title name="Вебхук на создание комплекта"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'bundle'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addCreateWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Вебхук на изменение комплекта"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'bundle'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addUpdateWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Вебхук на удаление комплекта"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'DELETE', 'type' => 'bundle'])->first())
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
