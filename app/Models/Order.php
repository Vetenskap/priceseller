<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'orderable_id',
        'orderable_type',
        'count',
        'price',
        'user_id',
        'organization_id'
    ];

    public function orderable()
    {
        return $this->morphTo();
    }
}
