<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReport extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'status',
        'message',
        'supplier_id',
        'path'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function logs()
    {
        return $this->hasMany(SupplierReportLog::class);
    }
}
