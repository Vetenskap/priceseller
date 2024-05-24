<div>
    <x-blocks.main-block>
        <x-layouts.title name="Поставщики"/>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <select wire:change="store($event.target.value)">
            <option value="">Выберите поставщика</option>
            @foreach(auth()->user()->suppliers as $supplier)
                <option wire:key="{{$supplier->getKey()}}"
                        value="{{$supplier->id}}">{{$supplier->name}}</option>
            @endforeach
        </select>
    </x-blocks.main-block>
    @foreach($email->suppliers as $supplier)
        <livewire:email-supplier.email-supplier-edit wire:key="{{$supplier->pivot->id}}" :email-supplier-id="$supplier->pivot->id"/>
    @endforeach
</div>
