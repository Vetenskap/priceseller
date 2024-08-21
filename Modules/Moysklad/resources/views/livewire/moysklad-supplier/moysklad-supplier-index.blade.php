<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового поставщика"/>
            <x-information>
                Вы можете привязать своих поставщиков с Моего Склада к своим существующим.
            </x-information>
        </x-blocks.main-block>
        <div x-data="{ open: false }">
            <x-blocks.main-block>
                <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
            </x-blocks.main-block>
            <div x-show="open">
                <x-blocks.flex-block>
                    <x-dropdowns.dropdown-select name="supplier_id"
                                                 :items="auth()->user()->suppliers"
                                                 field="form.supplier_id"
                                                 :current-id="$form->supplier_id"
                                                 :current-items="$moysklad->suppliers"
                                                 current-items-option-value="supplier_id"
                    >Ваш поставщик (priceseller)
                    </x-dropdowns.dropdown-select>
                    <x-dropdowns.dropdown-select name="moysklad_supplier_uuid"
                                                 :items="$moyskladSuppliers"
                                                 field="form.moysklad_supplier_uuid"
                                                 :current-id="$form->moysklad_supplier_uuid"
                                                 :current-items="$moysklad->suppliers"
                                                 current-items-option-value="moysklad_supplier_uuid"
                    >Ваш поставщик (Мой склад)
                    </x-dropdowns.dropdown-select>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </x-blocks.main-block>
            </div>
        </div>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        @if($moysklad->suppliers->isNotEmpty())
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($moysklad->suppliers as $supplier)
                <livewire:moysklad::moysklad-supplier.moysklad-supplier-edit
                    :moysklad="$moysklad" wire:key="{{$supplier->id}}" :moysklad-supplier="$supplier"/>
            @endforeach
        @else
            <x-blocks.main-block>
                <x-information>Вы пока ещё не добавляли поставщиков</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

