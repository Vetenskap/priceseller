<?php

namespace Modules\VoshodApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VoshodApi\Database\Factories\VoshodApiReportFactory;

class VoshodApiReport extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): VoshodApiReportFactory
    {
        //return VoshodApiReportFactory::new();
    }
}
