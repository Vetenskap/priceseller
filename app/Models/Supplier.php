<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

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
}
