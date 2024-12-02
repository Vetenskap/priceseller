<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends MainModel
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')->as('subscribe');
    }
}
