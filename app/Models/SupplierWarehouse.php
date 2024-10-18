<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierWarehouse extends Model
{
    use HasFactory, HasUuids;

    public $fillable = [
        'name',
        'supplier_id',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemSupplierWarehouseStock::class, 'supplier_warehouse_id', 'id');
    }
}
