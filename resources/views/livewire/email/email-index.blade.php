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
        @empty($emails->count())
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет почты"/>
            </x-blocks.main-block>
        @endempty
        @foreach($emails as $email)
            <x-table.table-item wire:key="{{$email->getKey()}}" wire:poll>
                <a href="{{route('email-show', ['email' => $email->getKey()])}}" wire:navigate.hover>

                    <x-layouts.simple-text :name="$email->name"/>

                </a>
                <a href="{{route('email-show', ['email' => $email->getKey()])}}" wire:navigate.hover>

                    <x-layouts.simple-text :name="$email->address"/>

                </a>
                <x-danger-button wire:click="destroy({{$email}})">Удалить</x-danger-button>
                <x-inputs.switcher :checked="$email->open" wire:change="changeOpen({{$email}})"/>
            </x-table.table-item>
        @endforeach
    </x-layouts.main-container>
</div>
