<?php

namespace App\Livewire\Employee;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\EmployeePermission;
use App\Models\Permission;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeIndex extends BaseComponent
{
    use WithPagination;

    public $currentEmployee;

    public $permissions = [];

    public function save(): void
    {
        foreach ($this->permissions as $id => $rules) {
            EmployeePermission::updateOrCreate([
                'employee_id' => $this->currentEmployee,
                'permission_id' => $id,
            ], array_merge([
                'employee_id' => $this->currentEmployee,
                'permission_id' => $id,
            ], $rules));
        }

        $this->reset('currentEmployee', 'permissions');

        \Flux::modal('edit-rules')->close();
    }

    public function addEmployee($id): void
    {
        $employee = Employee::find($id);

        $this->currentEmployee = $id;

        $this->permissions = $employee->permissions->mapWithKeys(function (Permission $permission) {
            return [$permission->id => [
                'view' => (bool) $permission->pivot->view,
                'create' => (bool) $permission->pivot->create,
                'update' => (bool) $permission->pivot->update,
                'delete' => (bool) $permission->pivot->delete,
            ]];
        })->toArray();
    }

    #[Computed]
    public function employees()
    {
        return \auth()
            ->user()
            ->employees()
            ->paginate();
    }

    public function render()
    {
        return view('livewire.employee.employee-index');
    }
}
