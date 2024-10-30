<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Вебхук"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'demand'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook({{$webhook}})">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
