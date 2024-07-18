<?php

namespace Modules\Order\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
        $this->write_off = true;
        return $this->save();
    }
}
