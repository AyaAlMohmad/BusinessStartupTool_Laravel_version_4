<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    use Auditable;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'status',
        'registration_date',
        'last_login',
        'progress',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'registration_date' => 'datetime',
        'last_login' => 'datetime',
    ];
// في User.php
public function roles()
{
    return $this->belongsToMany(Role::class,'role_users','user_id','role_id');
}

// دالة مساعدة للتحقق من الصلاحيات
public function hasPermission($permissionSlug)
{
    return $this->roles()->whereHas('permissions', function ($q) use ($permissionSlug) {
        $q->where('slug', $permissionSlug);
    })->exists();
}

public function hasRole($roleName)
{
    return $this->roles()->where('name', $roleName)->exists();
}

public function isAdmin()
{
    return $this->is_admin == 1;
}
// في ملف app/Models/User.php
public function hasAnyPermission(array $permissions): bool
{
    return $this->roles()
        ->whereHas('permissions', function($query) use ($permissions) {
            $query->whereIn('slug', $permissions);
        })
        ->exists();
}

public function hasAllPermissions(array $permissions): bool
{
    $count = $this->roles()
        ->whereHas('permissions', function($query) use ($permissions) {
            $query->whereIn('slug', $permissions);
        })
        ->count();

    return $count === count($permissions);
}
// public function role()
// {
//     return $this->belongsTo(Role::class);
// }
}
