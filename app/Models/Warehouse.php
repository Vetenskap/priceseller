<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\WriteOffWarehouseStock;

class Warehouse extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function stocks()
    {
        return $this->hasMany(ItemWarehouseStock::class);
    }
}
