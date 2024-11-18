<div>
    <x-layouts.header :name="$email->name"/>

    <x-layouts.actions>
        @if($this->user()->can('delete-emails'))
                <flux:modal.trigger name="delete-email">
                    <flux:button variant="danger">Удалить</flux:button>
                </flux:modal.trigger>

                <flux:modal name="delete-email" class="min-w-[22rem] space-y-6">
                    <div>
                        <flux:heading size="lg">Удалить почту?</flux:heading>

                        <flux:subheading>
                            <p>Вы действительно хотите удалить эту почту?</p>
                            <p>Это действие нельзя будет отменить.</p>
                        </flux:subheading>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer/>

                        <flux:modal.close>
                            <flux:button variant="ghost">Отменить</flux:button>
                        </flux:modal.close>

                        <flux:button wire:click="destroy" variant="danger">Удалить</flux:button>
                    </div>
                </flux:modal>
        @endif
    </x-layouts.actions>

    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general" icon="home">Основное</flux:tab>
                    <flux:tab name="suppliers" icon="truck">Поставщики</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <div class="flex">
                            <flux:switch wire:model.live="form.open" label="Включен"/>
                        </div>
                        <div class="flex gap-6">
                            <flux:input wire:model.live="form.name" label="Наименование" required/>
                            <flux:input wire:model.live="form.address" label="Адрес" type="email" required/>
                            <flux:input wire:model.live="form.password" label="Пароль" type="password" required/>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="suppliers">
                <livewire:email-supplier.email-supplier-index :email="$email"/>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
    @if($this->user()->can('update-emails'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>

