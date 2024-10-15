<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bundle extends Model
{
    use HasUuids;

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'bundle_items')->withPivot(['multiplicity', 'updated_at', 'created_at']);
    }

    protected $fillable = [
        'code',
        'name',
        'user_id',
        'ms_uuid',
        'id'
    ];

    const MAINATTRIBUTES = [
        [
            'name' => 'name',
            'label' => 'Наименование'
        ],
        [
            'name' => 'code',
            'label' => 'Код клиента'
        ],
    ];

    public function ozonItems()
    {
        return $this->morphMany(OzonItem::class, 'ozonitemable');
    }

    public function wbItems()
    {
        return $this->morphMany(WbItem::class, 'wbitemable');
    }
}
