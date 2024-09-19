<div>
    <x-blocks.main-block>
        <x-layouts.title name="Значения остатков"/>
    </x-blocks.main-block>
    <div x-data="{ open: false }">
        <x-blocks.main-block>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-blocks.main-block>
        <div x-show="open">
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="name"
                                           field="form.name"
                                           type="text"
                >Значение в прайсе
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="value"
                                           field="form.value"
                                           type="number"
                >Какой остаток ставить
                </x-inputs.input-with-label>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </div>
    </div>
    <x-blocks.main-block>
        <x-layouts.title name="Все значение остаток"/>
    </x-blocks.main-block>
    @if($emailSupplier->stockValues->isNotEmpty())
        <x-blocks.main-block>
            <x-success-button wire:click="update">Сохранить</x-success-button>
        </x-blocks.main-block>
        @foreach($emailSupplier->stockValues as $stockValue)
            <livewire:email-supplier-stock-value.email-supplier-stock-value-edit :stock-value="$stockValue" :email-supplier="$emailSupplier" wire:key="{{$stockValue->getKey()}}"/>
        @endforeach
    @else
        <x-blocks.main-block>
            <x-information>Вы пока ещё не добавляли значения остатков</x-information>
        </x-blocks.main-block>
    @endif
</div>
