<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MainModel extends Model
{
    public function getCreatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value)->timezone(Helpers::getUserTimeZone());
    }
    public function getUpdatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value)->timezone(Helpers::getUserTimeZone());
    }
}
