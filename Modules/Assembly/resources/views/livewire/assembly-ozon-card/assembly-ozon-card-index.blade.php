<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <div class="flex justify-between">
                <flux:heading size="xl">Карточка ОЗОН</flux:heading>
                <flux:button wire:click="save">Сохранить</flux:button>
            </div>
            <div>
                <flux:card>
                    <div class="flex gap-12">
                        <flux:heading
                            :size="match($mainFields['name_heading']['size_level']) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                            Наименование
                        </flux:heading>
                        <flux:input type="range" min="1" max="3" step="1"
                                    wire:model.live="mainFields.name_heading.size_level"/>
                    </div>
                </flux:card>
                <flux:card>
                    <div class="flex">
                        <flux:card class="w-1/4 space-y-6 text-center">
                            <img src="https://www.susu.ru/sites/default/files/field/image/1_53.png"/>
                            <flux:button variant="danger" size="sm">Пожаловаться</flux:button>
                        </flux:card>
                        <flux:card class="space-y-6 w-full">
                            <div class="flex gap-12">
                                <flux:button
                                    :size="match($mainFields['button_label']['size_level']) { '1' => 'xs', '2' => 'sm', '3' => 'base' }">
                                    Получить этикетку
                                </flux:button>
                                <flux:input type="range" min="1" max="3" step="1"
                                            wire:model.live="mainFields.button_label.size_level"/>
                            </div>
                            @foreach($selectedFields as $ssfield => $parameters)
                                <div class="flex gap-2 items-center" wire:key="{{$ssfield}}">

                                    <flux:button icon="chevron-{{$this->upOrDown($ssfield)}}"
                                                 wire:click="upField({{json_encode($ssfield)}})"/>

                                    <div class="flex">

                                    </div>
                                    @if($parameters['size_level'] < 5)
                                        <flux:subheading
                                            class="text-nowrap"
                                            style="color: {{ $parameters['color'] }};"
                                            :size="match($parameters['size_level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$parameters['label']}}</flux:subheading>
                                    @else
                                        <flux:heading
                                            class="text-nowrap"
                                            style="color: {{ $parameters['color'] }};"
                                            :size="match($parameters['size_level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$parameters['label']}}</flux:heading>
                                    @endif

                                    <flux:input type="color"
                                                wire:model.live="selectedFields.{{$ssfield}}.color"/>

                                    <flux:input type="range" min="1" max="7" step="1"
                                                wire:model.live="selectedFields.{{$ssfield}}.size_level"/>

                                    <flux:button icon="trash" variant="danger" size="sm"
                                                 wire:click="deleteField({{ json_encode($ssfield) }}, {{ json_encode($parameters) }})"
                                                 wire:target="deleteField({{ json_encode($ssfield) }}, {{ json_encode($parameters) }})"
                                                 wire:confirm="Вы действительно хотите удалить это поле? Это действие нельзя будет отменить."/>
                                </div>
                                <flux:separator />
                            @endforeach
                            <div>
                                <flux:dropdown>
                                    <flux:button icon-trailing="chevron-down">Добавить поле</flux:button>
                                    <flux:menu>
                                        @foreach($fields as $name => $field)
                                            <flux:menu.submenu heading="{{$name}}">
                                                @foreach($field as $name2 => $field2)
                                                    @if(is_string($name2))
                                                        <flux:menu.submenu heading="{{$name2}}">
                                                            @foreach($field2 as $name3 => $field3)
                                                                @if(is_string($name3))
                                                                    <flux:menu.submenu heading="{{$name2}}">
                                                                        @foreach($field3 as $name4 => $field4)
                                                                            <flux:menu.item
                                                                                wire:click="addField({{json_encode($field4)}})">{{$field4['label']}}</flux:menu.item>
                                                                        @endforeach
                                                                    </flux:menu.submenu>
                                                                @else
                                                                    <flux:menu.item
                                                                        wire:click="addField({{json_encode($field3)}})">{{$field3['label']}}</flux:menu.item>
                                                                @endif
                                                            @endforeach
                                                        </flux:menu.submenu>
                                                    @else
                                                        <flux:menu.item
                                                            wire:click="addField({{json_encode($field2)}})">{{$field2['label']}}</flux:menu.item>
                                                    @endif
                                                @endforeach
                                            </flux:menu.submenu>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                            <flux:card>
                                <div class="flex gap-4">
                                    @foreach($selectedAdditionalFields as $ssfield => $parameters)
                                        <div wire:key="{{$ssfield}}">

                                            <div class="flex gap-4">
                                                @if($parameters['size_level'] < 5)
                                                    <flux:subheading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['size_level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$parameters['label']}}</flux:subheading>
                                                @else
                                                    <flux:heading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['size_level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$parameters['label']}}</flux:heading>
                                                @endif
                                                <flux:button icon="trash" variant="danger" size="sm"
                                                             wire:click="deleteField({{ json_encode($ssfield) }}, {{ json_encode($parameters) }})"
                                                             wire:target="deleteField({{ json_encode($ssfield) }}, {{ json_encode($parameters) }})"
                                                             wire:confirm="Вы действительно хотите удалить это поле? Это действие нельзя будет отменить."/>
                                            </div>

                                            <flux:input type="color"
                                                        wire:model.live="selectedAdditionalFields.{{$ssfield}}.color"/>

                                            <flux:input type="range" min="1" max="7" step="1"
                                                        wire:model.live="selectedAdditionalFields.{{$ssfield}}.size_level"/>
                                        </div>
                                    @endforeach
                                    <flux:dropdown>
                                        <flux:button icon-trailing="chevron-down">Добавить поле</flux:button>
                                        <flux:menu>
                                            @foreach($fields as $name => $field)
                                                <flux:menu.submenu heading="{{$name}}">
                                                    @foreach($field as $name2 => $field2)
                                                        @if(is_string($name2))
                                                            <flux:menu.submenu heading="{{$name2}}">
                                                                @foreach($field2 as $name3 => $field3)
                                                                    @if(is_string($name3))
                                                                        <flux:menu.submenu heading="{{$name2}}">
                                                                            @foreach($field3 as $name4 => $field4)
                                                                                <flux:menu.item
                                                                                    wire:click="addAdditionalField({{json_encode($field4)}})">{{$field4['label']}}</flux:menu.item>
                                                                            @endforeach
                                                                        </flux:menu.submenu>
                                                                    @else
                                                                        <flux:menu.item
                                                                            wire:click="addAdditionalField({{json_encode($field3)}})">{{$field3['label']}}</flux:menu.item>
                                                                    @endif
                                                                @endforeach
                                                            </flux:menu.submenu>
                                                        @else
                                                            <flux:menu.item
                                                                wire:click="addAdditionalField({{json_encode($field2)}})">{{$field2['label']}}</flux:menu.item>
                                                        @endif
                                                    @endforeach
                                                </flux:menu.submenu>
                                            @endforeach
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </flux:card>
                        </flux:card>
                    </div>
                </flux:card>
            </div>
        </flux:card>
    </x-blocks.main-block>
</div>