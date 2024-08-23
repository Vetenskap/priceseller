<?php

namespace Modules\Moysklad\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Moysklad\Database\Factories\MoyskladFactory;

class Moysklad extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_key',
        'user_id',
    ];

    public function warehouses()
    {
        return $this->hasMany(MoyskladWarehouseWarehouse::class);
    }

    public function suppliers()
    {
        return $this->hasMany(MoyskladSupplierSupplier::class);
    }

    public function organizations()
    {
        return $this->hasMany(MoyskladOrganizationOrganization::class);
    }

    public function webhooks()
    {
        return $this->hasMany(MoyskladWebhook::class);
    }

    public function itemMainAttributeLinks()
    {
        return $this->hasMany(MoyskladItemMainAttributeLink::class);
    }

    public function itemAdditionalAttributeLinks()
    {
        return $this->hasMany(MoyskladItemAdditionalAttributeLink::class);
    }

    public function itemsOrders()
    {
        return $this->hasMany(MoyskladItemOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(MoyskladItemOrder::class);
    }

}
