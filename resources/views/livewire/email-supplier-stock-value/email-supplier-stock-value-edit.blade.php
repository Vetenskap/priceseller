<div>
    <div class="flex gap-6">
        <flux:input wire:model="form.name" label="Значение в прайсе" required/>
        <flux:input wire:model="form.value" label="Какой остаток ставить" required/>
        <div class="self-end">
            <flux:button
                variant="danger"
                wire:click="destroy"
                wire:confirm="Вы действительно хотите удалить это значение остатка?"
            >Удалить</flux:button>
        </div>
    </div>
</div>
