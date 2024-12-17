<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MoyskladWarehousesUnloadTime extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'time',
        'moysklad_id',
    ];
}
