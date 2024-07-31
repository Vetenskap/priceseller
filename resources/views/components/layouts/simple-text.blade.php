@props(['name'])
<p {{ $attributes->merge(['class' => "text-sm font-medium text-gray-900 dark:text-gray-100"]) }} title="{{$name}}">
    {{\Illuminate\Support\Str::limit($name, 29)}}
</p>
