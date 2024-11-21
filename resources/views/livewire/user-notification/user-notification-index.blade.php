<div>
    <flux:modal name="link-telegram" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="xl">Привязка телеграма</flux:heading>
        </div>

        <flux:subheading>Создайте ссылку телеграм и перейдите по ней. Никому не передавайте данную ссылку.</flux:subheading>
        @if($token)
            <flux:link href="{{ config('services.telegram-bot-api.link') . '?start=' . $token }}">Ссылка</flux:link>
        @else
            <flux:button wire:click="createLink" icon-trailing="plus">Создать ссылку</flux:button>
        @endif
    </flux:modal>

    <x-layouts.header name="Уведомления"/>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading class="text-center" size="xl">Телеграм</flux:heading>
                @if($userNotification)
                    <div class="flex">
                        <flux:switch wire:model.live="enabled_telegram" label="Уведомления в телеграм"/>
                    </div>
                @else
                    <flux:modal.trigger name="link-telegram">
                        <flux:button icon-trailing="plus">Привязать</flux:button>
                    </flux:modal.trigger>
                @endif
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:checkbox.group wire:model.live="actionsIds" label="Уведомления">
                    @foreach(\App\Models\NotificationAction::all() as $action)
                        <flux:checkbox checked
                                       :value="$action->getKey()"
                                       :label="$action->label"
                                       :description="$action->description"
                        />
                    @endforeach
                </flux:checkbox.group>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
    {!! $this->renderSaveButton() !!}
</div>
