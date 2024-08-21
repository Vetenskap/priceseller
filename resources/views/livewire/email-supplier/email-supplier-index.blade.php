<div>
    <x-titles.title-header>Поставщики</x-titles.title-header>


    <div x-data="{ open: false}">
        <x-layouts.actions>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-layouts.actions>
        <x-layouts.main-container x-show="open">
            <x-blocks.main-block>
                <x-layouts.title name="Добавление нового поставщика"/>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-titles.sub-title name="Основная информация"/>
            </x-blocks.main-block>

            <x-blocks.main-block>
                <x-dropdowns.dropdown-select :items="auth()->user()->suppliers"
                                             :current-id="$form->supplier_id"
                                             name="supplier_id"
                                             field="form.supplier_id"
                >Поставщик
                </x-dropdowns.dropdown-select>
            </x-blocks.main-block>
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="email"
                                           type="email"
                                           field="form.email"
                >Почта
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="filename"
                                           type="text"
                                           field="form.filename"
                >Наименование файла
                </x-inputs.input-with-label>
            </x-blocks.flex-block>

            <x-blocks.main-block>
                <x-titles.sub-title name="Информация по файлу"/>
            </x-blocks.main-block>

            <x-blocks.flex-block>
                <x-inputs.input-with-label name="header_article"
                                           type="number"
                                           field="form.header_article"
                >Артикул
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_brand"
                                           type="number"
                                           field="form.header_brand"
                >Бренд
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_price"
                                           type="number"
                                           field="form.header_price"
                >Цена
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_count"
                                           type="number"
                                           field="form.header_count"
                >Остаток
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
            <x-blocks.main-block>
                <x-success-button wire:click="store">Добавить</x-success-button>
            </x-blocks.main-block>
        </x-layouts.main-container>
    </div>
    <x-layouts.main-container wire:poll>
        <x-blocks.main-block>
            <x-layouts.title name="Список" />
        </x-blocks.main-block>
        @foreach($email->suppliers as $supplier)
            <livewire:email-supplier.email-supplier-edit wire:key="{{$supplier->pivot->id}}"
                                                         :email-supplier-id="$supplier->pivot->id"/>
        @endforeach
    </x-layouts.main-container>
</div>
