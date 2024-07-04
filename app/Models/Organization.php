<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\Order;
use Modules\Order\Models\SupplierOrderReport;

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

    public function supplierOrderReports()
    {
        return $this->hasMany(SupplierOrderReport::class);
    }
}
