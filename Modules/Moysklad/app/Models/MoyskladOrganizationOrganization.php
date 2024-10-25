<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladOrganizationOrganization extends MainModel
{
    protected $fillable = [
        'moysklad_organization_uuid',
        'organization_id',
        'moysklad_id',
    ];

    public function moysklad(): BelongsTo
    {
        return $this->belongsTo(Moysklad::class);
    }
}
