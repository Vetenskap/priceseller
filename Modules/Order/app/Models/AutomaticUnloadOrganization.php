<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\AutomaticUnloadOrganizationFactory;

class AutomaticUnloadOrganization extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'automatic',
        'organization_id',
    ];

}
