<div>
    <flux:card class="space-y-6">
        <flux:heading size="xl">Список</flux:heading>
        <flux:table :paginate="$this->employees">
            <flux:columns>
                <flux:column>Имя</flux:column>
                <flux:column>Почта</flux:column>
            </flux:columns>
            <flux:rows>
                @foreach($this->employees as $employee)
                    <flux:row :key="$employee->getKey()">
                        <flux:cell>{{$employee->name}}</flux:cell>
                        <flux:cell>{{$employee->email}}</flux:cell>
                        <flux:cell>
                            <flux:modal.trigger name="edit-rules">
                                <flux:button icon="adjustments-horizontal" wire:click="addEmployee({{$employee->getKey()}})"/>
                            </flux:modal.trigger>
                        </flux:cell>
                        <flux:cell>
                            <flux:button icon="trash" variant="danger" wire:click="destroy({{$employee->getKey()}})"/>
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </flux:card>
    <flux:modal name="edit-rules" class="space-y-6">
        <div>
            <flux:heading size="lg">Редактирование прав</flux:heading>
            <flux:subheading>Вы можете установить права для этого сотрудника.</flux:subheading>
        </div>

        <flux:button wire:click="save">Сохранить</flux:button>

        <flux:table>
            <flux:columns>
                <flux:column>Право</flux:column>
                <flux:column>Просмотр</flux:column>
                <flux:column>Создание</flux:column>
                <flux:column>Редактирование</flux:column>
                <flux:column>Удаление</flux:column>
            </flux:columns>
            <flux:rows>
                @foreach(\App\Models\Permission::where('type', 'employee')->get() as $permission)
                    <flux:row>
                        <flux:cell>{{$permission->name}}</flux:cell>
                        <flux:cell>
                            <flux:switch wire:model="permissions.{{$permission->getKey()}}.view"/>
                        </flux:cell>
                        <flux:cell>
                            <flux:switch wire:model="permissions.{{$permission->getKey()}}.create"/>
                        </flux:cell>
                        <flux:cell>
                            <flux:switch wire:model="permissions.{{$permission->getKey()}}.update"/>
                        </flux:cell>
                        <flux:cell>
                            <flux:switch wire:model="permissions.{{$permission->getKey()}}.delete"/>
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </flux:modal>
</div>
