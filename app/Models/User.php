<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Sanctum\HasApiTokens;
use Modules\Order\Models\Order;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, CanResetPassword
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getTimeZoneAttribute ($value): string
    {
        return $value == config('app.timezone') || empty($value) ? config('app.timezone') : $value;
    }

    public function setTimeZoneAttribute($value)
    {
        $this->attributes['timezone'] = $value == config('app.timezone') || is_null($value) ? null : $value;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps()
            ->withPivot('expires');
    }

    public function isMainSub(): bool
    {
        return $this->permissions()->where('value', 'main_sub')->where('expires', '>', now())->exists();
    }

    public function isMsSub(): bool
    {
        return $this->permissions()->where('value', 'ms_sub')->where('expires', '>', now())->exists();
    }

    public function isAvitoSub(): bool
    {
        return $this->permissions()->where('value', 'avito_sub')->where('expires', '>', now())->exists();
    }

    public function isOzonFiveSub(): bool
    {
        return $this->permissions()->where('value', 'ozon_five_markets')->where('expires', '>', now())->exists();
    }

    public function isOzonSub()
    {
        return $this->isOzonFiveSub() || $this->isOzonTenSub();
    }

    public function isWbFiveSub(): bool
    {
        return $this->permissions()->where('value', 'wb_five_markets')->where('expires', '>', now())->exists();
    }

    public function isWbSub()
    {
        return $this->isWbFiveSub() || $this->isWbTenSub();
    }

    public function isOzonTenSub(): bool
    {
        return $this->permissions()->where('value', 'ozon_ten_markets')->where('expires', '>', now())->exists();
    }

    public function isWbTenSub(): bool
    {
        return $this->permissions()->where('value', 'wb_ten_markets')->where('expires', '>', now())->exists();
    }

    public function isAdmin(): bool
    {
        return $this->permissions()->where('value', 'admin')->where('expires', '>', now())->exists();
    }

    public function isSub()
    {
        return $this->isAdmin() || $this->isWbSub() || $this->isOzonSub();
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function ozonMarkets()
    {
        return $this->hasMany(OzonMarket::class);
    }

    public function wbMarkets()
    {
        return $this->hasMany(WbMarket::class);
    }

    public function itemsImportReports()
    {
        return $this->morphMany(ItemsImportReport::class, 'reportable');
    }

    public function itemsExportReports()
    {
        return $this->morphMany(ItemsExportReport::class, 'reportable');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isAdmin() || App::isLocal();
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function warehousesItemsExportReports()
    {
        return $this->hasMany(WarehousesItemsExportReport::class);
    }

    public function warehousesItemsImportReports()
    {
        return $this->hasMany(WarehousesItemsImportReport::class);
    }
}
