<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, GlobalStatus;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn () => $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE_USER);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::BAN_USER);
    }

    public function scopeManager($query)
    {
        $query->where('user_type', 'manager');
    }

    public function scopeStaff($query)
    {
        $query->where('user_type', 'staff');
    }
}
