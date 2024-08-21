<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSupplier extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'email',
        'filename',
        'header_article',
        'header_brand',
        'header_price',
        'header_count',
        'email_id',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockValues()
    {
        return $this->hasMany(EmailSupplierStockValue::class);
    }

    public function mainEmail()
    {
        return $this->belongsTo(Email::class, 'email_id', 'id');
    }

}
