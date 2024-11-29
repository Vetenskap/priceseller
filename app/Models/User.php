<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Sanctum\HasApiTokens;
use Modules\Assembly\Models\AssemblyProductSettings;
use Modules\BergApi\Models\BergApi;
use Modules\Moysklad\Models\Moysklad;
use Modules\Order\Models\Order;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\VoshodApi\Models\VoshodApi;

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

    protected static function booted()
    {
        static::updated(function (User $user) {
            if ($user->wasChanged('permissions')) {
                $user->enforceOzonMarketLimits();
                $user->enforceWbMarketLimits();
            }
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->where('type', 'main')
            ->withTimestamps()
            ->withPivot('expires');
    }

    public function isAdmin(): bool
    {
        return $this->permissions()->where('value', 'admin')->where('expires', '>', now())->exists();
    }

    public function existsPermission(string $permissionValue): bool
    {
        return $this->permissions()
            ->where('value', $permissionValue)
            ->where(function ($query) {
                $query->whereNull('expires')
                    ->orWhere('expires', '>', now());
            })
            ->exists();
    }

    public function enforceWbMarketLimits(): void
    {
        $maxMarkets = $this->maxAllowedMarkets('wb');

        $markets = $this->wbMarkets()->orderBy('created_at')->get();

        foreach ($markets as $index => $market) {
            $market->update(['open' => $index < $maxMarkets, 'close' => $index > $maxMarkets]);
        }
    }

    public function enforceOzonMarketLimits(): void
    {
        $maxMarkets = $this->maxAllowedMarkets('ozon');

        $markets = $this->ozonMarkets()->orderBy('created_at')->get();

        foreach ($markets as $index => $market) {
            $market->update(['open' => $index < $maxMarkets, 'close' => $index > $maxMarkets]);
        }
    }

    public function maxAllowedMarkets(string $platform): int
    {
        $permissionMap = [
            'ozon' => [
                'ozon_five_markets' => 5,
                'ozon_ten_markets' => 10,
            ],
            'wb' => [
                'wb_five_markets' => 5,
                'wb_ten_markets' => 10,
            ],
        ];

        foreach ($permissionMap[$platform] ?? [] as $permission => $limit) {
            if ($this->existsPermission($permission)) {
                return $limit;
            }
        }

        return 0;
    }

    public function isSub(): bool
    {
        return $this->maxAllowedMarkets('ozon') || $this->maxAllowedMarkets('wb');
    }

    public function telegramLinks(): HasMany
    {
        return $this->hasMany(TelegramLink::class, 'user_id', 'id');
    }

    public function userNotification(): HasOne
    {
        return $this->hasOne(UserNotification::class, 'user_id', 'id');
    }

    public function userNotificationActionEnabled(string $action): ?bool
    {
        return $this->userNotification->actions()->whereHas('action', fn (Builder $query) => $query->where('name', $action))->first()?->enabled;
    }

    public function getTimeZoneAttribute ($value): string
    {
        return $value == config('app.timezone') || empty($value) ? config('app.timezone') : $value;
    }

    public function setTimeZoneAttribute($value): void
    {
        $this->attributes['timezone'] = $value == config('app.timezone') || is_null($value) ? null : $value;
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function ozonMarkets(): HasMany
    {
        return $this->hasMany(OzonMarket::class);
    }

    public function wbMarkets(): HasMany
    {
        return $this->hasMany(WbMarket::class);
    }

    public function itemsImportReports(): MorphMany
    {
        return $this->morphMany(ItemsImportReport::class, 'reportable');
    }

    public function itemsExportReports(): MorphMany
    {
        return $this->morphMany(ItemsExportReport::class, 'reportable');
    }

    public function bundlesExportReports(): HasMany
    {
        return $this->hasMany(BundlesExportReport::class);
    }

    public function bundlesImportReports(): HasMany
    {
        return $this->hasMany(BundlesImportReport::class);
    }

    public function bundleItemsExportReports(): HasMany
    {
        return $this->hasMany(BundleItemsExportReport::class);
    }

    public function bundleItemsImportReports(): HasMany
    {
        return $this->hasMany(BundleItemsImportReport::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isAdmin() || App::isLocal();
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function warehousesItemsExportReports(): HasMany
    {
        return $this->hasMany(WarehousesItemsExportReport::class);
    }

    public function warehousesItemsImportReports(): HasMany
    {
        return $this->hasMany(WarehousesItemsImportReport::class);
    }

    public function moysklad(): HasOne
    {
        return $this->hasOne(Moysklad::class);
    }

    public function itemAttributes(): HasMany
    {
        return $this->hasMany(ItemAttribute::class);
    }

    public function baseSettings(): HasOne
    {
        return $this->hasOne(UserBaseSetting::class);
    }

    public function voshodApi(): HasOne
    {
        return $this->hasOne(VoshodApi::class);
    }

    public function samsonApi(): HasOne
    {
        return $this->hasOne(SamsonApi::class);
    }

    public function bergApi(): HasOne
    {
        return $this->hasOne(BergApi::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(Bundle::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'user_id', 'id');
    }

    public function assemblyProductSettings(): HasMany
    {
        return $this->hasMany(AssemblyProductSettings::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
