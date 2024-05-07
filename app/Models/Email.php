<?php

namespace App\Models;

use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'password',
        'user_id',
    ];

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'email_suppliers')
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
