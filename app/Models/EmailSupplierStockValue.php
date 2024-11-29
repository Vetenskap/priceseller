<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSupplierStockValue extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'email_supplier_id',
    ];

    public function emailSupplier()
    {
        return $this->belongsTo(EmailSupplier::class);
    }
}
