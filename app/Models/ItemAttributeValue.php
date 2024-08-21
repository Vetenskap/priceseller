<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'item_id',
        'item_attribute_id',
    ];

    public function attribute()
    {
        return $this->belongsTo(ItemAttribute::class, 'item_attribute_id', 'id');
    }
}
