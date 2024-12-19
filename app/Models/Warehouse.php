<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\Models\WriteOffWarehouseStock;

class Warehouse extends MainModel
{

    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemWarehouseStock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
