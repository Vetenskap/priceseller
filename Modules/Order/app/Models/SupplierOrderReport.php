<?php

namespace Modules\Order\Models;

use App\Models\Supplier;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\SupplierOrderReportFactory;

class SupplierOrderReport extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'supplier_id',
        'organization_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
