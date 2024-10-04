<div>
    <flux:card class="space-y-6">
        <flux:heading size="xl">Значения остатков</flux:heading>
        <flux:subheading>Какое слово/значение в прайсе заменять на определенный остаток</flux:subheading>

        <flux:modal name="create-email-supplier-stock-value-{{$emailSupplier->getKey()}}" class="md:w-96 space-y-6">
            <div>
                <flux:heading size="lg">Добавление склада</flux:heading>
            </div>

            <flux:input wire:model="form.name" label="Значение в прайсе" required/>
            <flux:input wire:model="form.value" label="Какой остаток ставить" required/>

            <div class="flex">
                <flux:spacer/>

                <flux:button variant="primary" wire:click="store">Создать</flux:button>
            </div>
        </flux:modal>

        <div>
            <flux:modal.trigger name="create-email-supplier-stock-value-{{$emailSupplier->getKey()}}">
                <flux:button>Добавить</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Все значения остатков</flux:heading>

            @if($emailSupplier->stockValues->isNotEmpty())
                <flux:button wire:click="update">Сохранить</flux:button>
                @foreach($emailSupplier->stockValues as $stockValue)
                    <livewire:email-supplier-stock-value.email-supplier-stock-value-edit :stock-value="$stockValue"
                                                                                         :email-supplier="$emailSupplier"
                                                                                         wire:key="{{$stockValue->getKey()}}"/>
                @endforeach
            @else
                <flux:subheading>Вы пока ещё не добавляли значения остатков</flux:subheading>
            @endif
        </flux:card>

    </flux:card>
</div>
