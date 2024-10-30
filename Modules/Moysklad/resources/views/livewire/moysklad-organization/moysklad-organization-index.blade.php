<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление новой организации"/>
            <x-information>
                Вы можете привязать свои организации с Моего Склада к своим существующим.
            </x-information>
        </x-blocks.main-block>
        <div x-data="{ open: false }">
            <x-blocks.main-block>
                <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
            </x-blocks.main-block>
            <div x-show="open">
                <x-blocks.flex-block>
                    <x-dropdowns.dropdown-select name="organization_id"
                                                 :items="auth()->user()->organizations"
                                                 field="form.organization_id"
                                                 :current-id="$form->organization_id"
                                                 :current-items="$moysklad->organizations"
                                                 current-items-option-value="organization_id"
                    >Ваша организация (priceseller)
                    </x-dropdowns.dropdown-select>
                    <x-dropdowns.dropdown-select name="moysklad_organization_uuid"
                                                 :items="$moyskladOrganizations"
                                                 field="form.moysklad_organization_uuid"
                                                 :current-id="$form->moysklad_organization_uuid"
                                                 :current-items="$moysklad->organizations"
                                                 current-items-option-value="moysklad_organization_uuid"
                    >Ваша организация (Мой склад)
                    </x-dropdowns.dropdown-select>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </x-blocks.main-block>
            </div>
        </div>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        @if($moysklad->organizations->isNotEmpty())
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($moysklad->organizations as $organization)
                <livewire:moysklad::moysklad-organization.moysklad-organization-edit
                    :moysklad="$moysklad" wire:key="{{$organization->id}}" :organization="$organization"/>
            @endforeach
        @else
            <x-blocks.main-block>
                <x-information>Вы пока ещё не добавляли организации</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

