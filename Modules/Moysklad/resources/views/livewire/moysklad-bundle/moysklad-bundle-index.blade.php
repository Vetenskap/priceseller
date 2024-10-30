<div>
    <livewire:moysklad::moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-index
        :moysklad="$moysklad"/>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading>Выгрузка комплектов с Моего склада</flux:heading>
            <flux:button wire:click="importApi">Выгрузить по АПИ</flux:button>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Вебхуки</flux:heading>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на создание комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addCreateWebhook">Добавить</flux:button>
                @endif
            </flux:card>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на изменение комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addUpdateWebhook">Добавить</flux:button>
                @endif
            </flux:card>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на удаление комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'DELETE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addDeleteWebhook">Добавить</flux:button>
                @endif
            </flux:card>
        </flux:card>
    </x-blocks.main-block>
</div>
