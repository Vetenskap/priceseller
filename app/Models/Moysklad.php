<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moysklad extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'user_id',
    ];

    public function warehouses(): HasMany
    {
        return $this->hasMany(MoyskladWarehouse::class);
    }
}
