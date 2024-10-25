<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePermission extends MainModel
{
    public $fillable = [
        'view',
        'create',
        'update',
        'delete',
        'employee_id',
        'permission_id',
    ];
}
