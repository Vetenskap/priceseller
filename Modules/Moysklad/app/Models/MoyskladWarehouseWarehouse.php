<?php

namespace Modules\Moysklad\Models;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Moysklad\Database\Factories\MoyskladWarehouseWarehouseFactory;

class MoyskladWarehouseWarehouse extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'moysklad_warehouse_uuid',
        'warehouse_id',
        'moysklad_id'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function moysklad()
    {
        return $this->belongsTo(Moysklad::class);
    }

}
