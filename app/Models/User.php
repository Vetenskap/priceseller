<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps()
            ->wherePivot('expires', '>', now()->timestamp)
            ->withPivot('expires');
    }

    public function is_main_sub()
    {
        return $this->permissions()->where('value', 'main_sub')->where('expires', '>', now()->timestamp)->exists();
    }

    public function is_ms_sub()
    {
        return $this->permissions()->where('value', 'ms_sub')->where('expires', '>', now()->timestamp)->exists();
    }

    public function is_avito_sub()
    {
        return $this->permissions()->where('value', 'avito_sub')->where('expires', '>', now()->timestamp)->exists();
    }

    public function isAdmin()
    {
        return $this->permissions()->where('value', 'admin')->where('expires', '>', now()->timestamp)->exists();
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
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
}
