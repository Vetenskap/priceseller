<div>
    <x-layouts.header name="Почта"/>

    <flux:modal name="create-email" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание почты</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" required/>
        <flux:input wire:model="form.address" label="Адрес" type="email" required/>
        <flux:input wire:model="form.password" label="Адрес" type="password" required/>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-layouts.actions>
        <flux:modal.trigger name="create-email">
            <flux:button>Добавить</flux:button>
        </flux:modal.trigger>
    </x-layouts.actions>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        @if($emails->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Адрес"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($emails as $email)
                    <a href="{{route('email.edit', ['email' => $email->getKey()])}}" wire:key="{{$email->getKey()}}">
                        <x-table.table-item>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$email->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$email->address"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-information>{{$email->updated_at}}</x-information>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-inputs.switcher :checked="$email->open"
                                                   wire:change="changeOpen({{json_encode($email->getKey())}})"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет почты</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>
