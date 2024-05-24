@props(['json'])
@php
    $array = json_decode($json);
@endphp
<div>
    @foreach($array as $key => $value)
        @if(is_numeric($key))
            <x-layouts.simple-text name="{{$value}}" />
        @else
            <x-layouts.simple-text name="{{$key}}: {{$value}}" />
        @endif
    @endforeach
</div>
