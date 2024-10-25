<?php

namespace Modules\Order\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MainModel;

class Order extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'number',
        'orderable_id',
        'orderable_type',
        'count',
        'state',
        'price',
        'organization_id',
        'currency_code',
        'write_off'
    ];

    public function orderable()
    {
        return $this->morphTo();
    }

    public function writeOffStocks()
    {
        return $this->hasMany(WriteOffItemWarehouseStock::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function markWriteOff(): bool
    {
        return $this->update(['write_off' => true]);
    }

    public function markAccepted(): bool
    {
        return $this->update(['state' => 'old']);
    }
}
