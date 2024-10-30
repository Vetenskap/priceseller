@props(['items'])

@foreach($items as $key => $value)
    <flux:menu.submenu heading="{{$key}}">
        @if(is_string($key))
            <x-flux-recursive-sub-menu-dropdown :items="$value"/>
        @else
            @foreach($value as $field)
                <flux:menu.item wire:click="addAdditionalField({{json_encode($field)}})">{{$field}}</flux:menu.item>
            @endforeach
        @endif
    </flux:menu.submenu>
@endforeach
