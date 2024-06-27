<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
