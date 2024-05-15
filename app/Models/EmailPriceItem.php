<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailPriceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'article',
        'brand',
        'price',
        'stock',
        'message',
        'status',
        'supplier_id',
        'item_id',
    ];
}
