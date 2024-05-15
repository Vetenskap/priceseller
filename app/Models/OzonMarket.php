<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OzonMarket extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public function items()
    {
        return $this->hasMany(OzonItem::class);
    }

    public function importReports()
    {
        return $this->morphMany(MarketImportReport::class, 'reportable');
    }

    public function exportReports()
    {
        return $this->morphMany(MarketExportReport::class, 'reportable');
    }

    public function relationships()
    {
        return $this->morphMany(MarketItemRelationship::class, 'relationshipable');
    }
}
