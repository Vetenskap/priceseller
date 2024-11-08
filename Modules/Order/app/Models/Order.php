<?php

namespace Modules\Order\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function writeOffStocks(): HasMany
    {
        return $this->hasMany(WriteOffItemWarehouseStock::class);
    }

    public function organization(): BelongsTo
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
