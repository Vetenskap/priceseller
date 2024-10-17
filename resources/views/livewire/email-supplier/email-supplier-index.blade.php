<div>
    <flux:modal name="create-email-supplier" class="!max-w-3xl space-y-6">
        <div>
            <flux:heading size="xl">Добавление поставщика</flux:heading>
        </div>

        <div class="flex gap-6 s">
            <div class="space-y-6">
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
                <flux:input wire:model="form.filename" label="Наименование файла" required/>
            </div>
            <div class="space-y-6">
                <div>
                    <flux:heading size="xl">Информация по файлу</flux:heading>
                </div>

                <flux:input wire:model="form.header_article" label="Артикул" type="number" required/>
                <flux:input wire:model="form.header_price" label="Цена" type="number" required/>
                <flux:input wire:model="form.header_count" label="Остаток" type="number" required/>
                <flux:input wire:model="form.header_brand" label="Бренд" type="number"/>
                <flux:input wire:model="form.header_warehouse" label="Склад" type="number"/>
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

    <x-blocks.main-block>
        <flux:heading size="xl">Список</flux:heading>
    </x-blocks.main-block>
    @foreach($email->suppliers as $supplier)
        <livewire:email-supplier.email-supplier-edit wire:key="{{$supplier->pivot->id}}"
                                                     :email-supplier-id="$supplier->pivot->id"/>
    @endforeach
</div>
