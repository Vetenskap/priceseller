<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use App\Models\Organization;
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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
