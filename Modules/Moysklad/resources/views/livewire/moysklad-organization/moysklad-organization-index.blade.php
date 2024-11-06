<div>
    <flux:modal name="create-moysklad-organization" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Связать нового поставщика</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите организацию..." label="Ваша организация (priceseller)"
                     wire:model="form.organization_id">

            @foreach(auth()->user()->organizations as $organization)
                <flux:option :value="$organization->getKey()">{{$organization->name}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите организацию..." label="Ваша организация (Мой склад)"
                     wire:model="form.moysklad_organization_uuid">

            @foreach($moyskladOrganizations as $moyskladOrganization)
                <flux:option :value="$moyskladOrganization['id']">{{$moyskladOrganization['name']}}</flux:option>
            @endforeach
        </flux:select>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Связать</flux:button>
        </div>
    </flux:modal>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Связать новую организацию</flux:heading>
            <flux:subheading>Вы можете привязать свои организации с Моего Склада к своим существующим.</flux:subheading>
            <div>
                <flux:modal.trigger name="create-moysklad-organization">
                    <flux:button>Связать</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Список</flux:heading>
            @if($this->organizations->isNotEmpty())
                <flux:table :paginate="$this->organizations">
                    <flux:columns>
                        <flux:column>Организация priceseller</flux:column>
                        <flux:column>Организация мой склад</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->organizations as $organization)
                            <flux:row :key="$organization->getKey()">
                                <flux:cell>{{collect($moyskladOrganizations)->firstWhere('id', $organization->moysklad_organization_uuid)['name']}}</flux:cell>
                                <flux:cell>{{$organization->organization->name}}</flux:cell>
                                <flux:cell align="right">
                                    <flux:button icon="trash"
                                                 variant="danger"
                                                 size="sm"
                                                 wire:click="destroy({{ json_encode($organization->getKey()) }})"
                                                 wire:target="destroy({{ json_encode($organization->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить эту организацию?"
                                    />
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>

