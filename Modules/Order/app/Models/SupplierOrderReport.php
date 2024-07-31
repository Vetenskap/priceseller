<?php

namespace Modules\Order\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\SupplierOrderReportFactory;

class SupplierOrderReport extends Model
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
