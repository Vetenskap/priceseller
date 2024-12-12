<?php

namespace App\Models;

use App\Models\Contracts\Reportable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends MainModel implements Reportable
{

    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'ms_uuid',
        'open',
        'use_brand',
        'user_id',
        'deleted_at',
        'unload_without_price',
    ];

    protected $casts = [
        'open' => 'boolean',
        'use_brand' => 'boolean',
        'unload_without_price' => 'boolean'
    ];

    public function emails()
    {
        return $this->belongsToMany(Email::class, 'email_suppliers')
            ->withPivot([
                'id',
                'header_article',
                'header_brand',
                'header_price',
                'header_count',
                'email',
                'filename'
            ]);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function priceItems()
    {
        return $this->hasMany(EmailPriceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(SupplierWarehouse::class);
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->name;
    }
}
