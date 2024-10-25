<?php

namespace Modules\VoshodApi\Models;

use App\Models\Supplier;
use App\Models\User;
use App\Models\MainModel;

class VoshodApi extends MainModel
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_key',
        'proxy_ip',
        'proxy_port',
        'proxy_login',
        'proxy_password',
        'user_id',
        'supplier_id'
    ];

    public function times()
    {
        return $this->hasMany(VoshodApiTime::class);
    }

    public function warehouses()
    {
        return $this->hasMany(VoshodApiWarehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemAdditionalAttributeLinks()
    {
        return $this->hasMany(VoshodApiItemAdditionalAttributeLink::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
