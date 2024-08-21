<div>
    <x-layouts.header name="Организации"/>
    <div x-data="{ open: false }">
        <x-layouts.actions>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-layouts.actions>
        <x-layouts.main-container x-show="open">
            <x-blocks.main-block>
                <x-layouts.title name="Добавление новой организации" />
            </x-blocks.main-block>
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </x-layouts.main-container>
    </div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список" />
        </x-blocks.main-block>
        @if($organizations->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($organizations as $organization)
                    <a href="{{route('organizations.edit', ['organization' => $organization->getKey()])}}" wire:key="{{$organization->getKey()}}">
                        <x-table.table-item>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$organization->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-information>{{$organization->updated_at}}</x-information>
                            </x-table.table-child>
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет организаций</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>
