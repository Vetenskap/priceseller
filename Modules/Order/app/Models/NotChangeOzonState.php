<?php

namespace Modules\Order\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\NotChangeOzonStateFactory;

class NotChangeOzonState extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['item_id', 'user_id'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
