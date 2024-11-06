<?php

namespace Modules\Moysklad\Models;

use App\Models\User;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Moysklad\Database\Factories\MoyskladFactory;

class Moysklad extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_key',
        'user_id',
        'clear_order_time',
        'enabled_orders',
        'diff_price',
        'enabled_diff_price',
        'enabled_recount_retail_markup',
        'link_recount_retail_markup_percent',
        'link_name_recount_retail_markup_percent',
        'link_label_recount_retail_markup_percent',
        'link_type_recount_retail_markup_percent',
        'price_type_uuids'
    ];

    protected $casts = [
        'price_type_uuids' => 'array'
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

    public function bundleMainAttributeLinks()
    {
        return $this->hasMany(MoyskladBundleMainAttributeLink::class);
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

    public function quarantine(): HasMany
    {
        return $this->hasMany(MoyskladQuarantine::class);
    }

}
