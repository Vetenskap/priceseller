<?php

namespace Modules\Moysklad\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoyskladSupplierSupplier extends MainModel
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'moysklad_supplier_uuid',
        'supplier_id',
        'moysklad_id',
        'moysklad_supplier_name'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
