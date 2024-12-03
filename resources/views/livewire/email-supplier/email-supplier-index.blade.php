<div>
    @if($this->user()->can('update-emails'))
        <flux:modal name="create-email-supplier" class="!max-w-3xl space-y-6">
            <div>
                <flux:heading size="xl">Добавление поставщика</flux:heading>
            </div>

            <div class="flex gap-6">
                <div class="space-y-6 w-1/2">
                    <div>
                        <flux:heading size="xl">Основная информация</flux:heading>
                    </div>

                    <flux:select variant="combobox" placeholder="Выберите поставщика..."
                                 wire:model="form.supplier_id" label="Поставщик">

                        @foreach($this->currentUser()->suppliers as $supplier)
                            <flux:option value="{{ $supplier->id }}">{{$supplier->name}}</flux:option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="form.email" label="Почта" type="email" required/>
                    <flux:input wire:model="form.filename" label="Наименование вложения" required/>
                    <flux:card class="flex gap-2">
                        <flux:badge color="red" class="h-fit">Важно!</flux:badge>
                        <flux:subheading>Чтение прайсов происходит во всех папках. Убедитесь, что все
                            старые прайсы имеют статус "прочитано", даже в корзине.
                        </flux:subheading>
                    </flux:card>
                </div>
                <div class="space-y-6 w-1/2">
                    <div>
                        <flux:heading size="xl">Информация по файлу</flux:heading>
                    </div>

                    <flux:input wire:model="form.header_article" label="Артикул поставщика" type="number" required/>
                    <flux:input wire:model="form.header_price" label="Цена" type="number" required/>
                    <flux:input wire:model="form.header_count" label="Остаток" type="number" required/>
                    <flux:input wire:model="form.header_brand" label="Бренд поставщика" type="number"/>
                    <flux:card class="flex gap-2">
                        <flux:badge color="red" class="h-fit">Важно!</flux:badge>
                        <flux:subheading>Обязательное поле если в поставщике выбран параметр "использовать бренд".</flux:subheading>
                    </flux:card>
                    <flux:input wire:model="form.header_warehouse" label="Склад" type="number"/>
                    <flux:card class="flex gap-2">
                        <flux:badge color="red" class="h-fit">Важно!</flux:badge>
                        <flux:subheading>Если в прайсе не указаны склады, то оставьте поле пустым. Будет
                            автоматически использоваться первый привязанный склад для загрузки остатков.
                            Для этого во вкладке "Склады" привяжите хотя бы один склад. В противном
                            случае остатки не будут выгружены.
                        </flux:subheading>
                    </flux:card>
                </div>
            </div>

            <div class="flex">
                <flux:spacer/>

                <flux:button variant="primary" wire:click="store">Создать</flux:button>
            </div>
        </flux:modal>

        <x-blocks.main-block>
            <flux:modal.trigger name="create-email-supplier">
                <flux:button>Добавить</flux:button>
            </flux:modal.trigger>
        </x-blocks.main-block>
    @endif

    <x-blocks.main-block>
        <flux:heading size="xl">Список</flux:heading>
    </x-blocks.main-block>
    @foreach($email->suppliers as $supplier)
        <livewire:email-supplier.email-supplier-edit wire:key="{{$supplier->pivot->id}}"
                                                     :email-supplier-id="$supplier->pivot->id"/>
    @endforeach
</div>
