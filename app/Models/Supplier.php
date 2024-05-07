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
                'header_start',
                'header_article_supplier',
                'header_article_manufacturer',
                'header_brand',
                'header_price',
                'header_count',
                'email',
                'filename'
            ]);
    }
}
