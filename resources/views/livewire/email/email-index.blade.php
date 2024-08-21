<div>
    <x-layouts.header name="Почта"/>

    <div x-data="{ open: false }">
        <x-layouts.actions>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-layouts.actions>

        <x-layouts.main-container x-show="open">
            <x-blocks.main-block>
                <x-layouts.title name="Добавление новой почты"/>
            </x-blocks.main-block>
            <x-blocks.flex-block>
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
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </x-layouts.main-container>
    </div>
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
