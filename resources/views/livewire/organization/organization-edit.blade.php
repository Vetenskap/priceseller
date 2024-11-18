<div>
    <x-layouts.header :name="$organization->name"/>
    <x-layouts.actions>
        @if($this->user()->can('delete-organizations'))
                <flux:button
                    variant="danger"
                    wire:click="destroy"
                    wire:confirm="Вы действительно хотите удалить эту организацию?"
                >Удалить
                </flux:button>
        @endif
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:input wire:model.live="form.name" label="Наименование" required/>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
    @if($this->user()->can('update-organizations'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>
