<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'ms_uuid',
        'open',
        'use_brand',
        'user_id',
        'unload_without_price'
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

    public function reports()
    {
        return $this->hasMany(SupplierReport::class);
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
}
