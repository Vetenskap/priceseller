<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePermission extends MainModel
{
    use HasFactory;

    public $fillable = [
        'view',
        'create',
        'update',
        'delete',
        'employee_id',
        'permission_id',
    ];
}
