<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Moysklad\Database\Factories\MoyskladFactory;

class Moysklad extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_key',
        'user_id',
    ];

    public function warehouses()
    {
        return $this->hasMany(MoyskladWarehouseWarehouse::class);
    }

}
