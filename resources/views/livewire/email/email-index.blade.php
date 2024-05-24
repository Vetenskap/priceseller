<div>
    <x-layouts.header name="Почта"/>

    <x-layouts.actions>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-layouts.actions>

    @if($showCreateBlock)
        <x-layouts.main-container>
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>


                <x-inputs.input-with-label name="address"
                                           type="email"
                                           field="form.address"
                >Адрес
                </x-inputs.input-with-label>


                <x-inputs.input-with-label name="password"
                                           type="password"
                                           field="form.password"
                >Пароль
                </x-inputs.input-with-label>

                <x-success-button wire:click="store">Добавить</x-success-button>

            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
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

                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($emails as $email)
                    <x-table.table-item wire:key="{{$email->getKey()}}" wire:poll>
                        <x-table.table-child>
                            <a href="{{route('email-show', ['email' => $email->getKey()])}}" wire:navigate.hover>

                                <x-layouts.simple-text :name="$email->name"/>

                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <a href="{{route('email-show', ['email' => $email->getKey()])}}" wire:navigate.hover>

                                <x-layouts.simple-text :name="$email->address"/>

                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-inputs.switcher :checked="$email->open" wire:change="changeOpen({{$email}})"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-danger-button wire:click="destroy({{$email}})">Удалить</x-danger-button>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет почты"/>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>
