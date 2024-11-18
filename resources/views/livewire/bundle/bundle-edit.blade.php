<div>
    <x-layouts.header :name="$bundle->name . ' (' . $bundle->code . ')'"/>
    <x-layouts.actions>
        @if($this->user()->can('delete-bundles'))
            <flux:button
                variant="danger"
                wire:click="destroy"
                wire:confirm="Вы действительно хотите удалить этот комплект?"
            >Удалить
            </flux:button>
        @endif
    </x-layouts.actions>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general">Основная информация</flux:tab>
                    <flux:tab name="plural">Связанные товары</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:input wire:model.live="form.name" label="Наименование"/>
                        <flux:input wire:model.live="form.code" label="Код" required/>
                        <flux:input wire:model.live="form.ms_uuid" label="МС UUID"/>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="plural">
                <livewire:bundle-plural.bundle-plural-index :bundle="$bundle"/>
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
    @if($this->user()->can('update-bundles'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>
