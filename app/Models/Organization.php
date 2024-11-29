<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\AutomaticUnloadOrganization;
use Modules\Order\Models\Order;
use Modules\Order\Models\SelectedWarehouses;
use Modules\Order\Models\SupplierOrderReport;

class Organization extends MainModel
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

    public function selectedOrdersWarehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'selected_warehouses');
    }

    public function automaticUnloadOrder()
    {
        return $this->hasOne(AutomaticUnloadOrganization::class);
    }
}
