<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\NewUserNotification;
use App\Notifications\RoleUpdatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\UserStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Department;
use App\Models\Publication;
use App\Models\Timesheet;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\TwoFactorAuthentication;

class User extends Authenticatable implements Auditable, TwoFactorAuthenticatable
{
    use HasFactory, Notifiable, HasRoles, AuditableTrait;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthentication;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'driver',
        'active',
        'change_password',
        'status_id',
        'status_notes',
        'phone',
        'soft_delete',
        'department_id',
        'is_wordpress_user',
        'contributor_status',
        'publication_id',
        'is_wordpress_user',
        
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret', 'two_factor_recovery_codes'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'change_password' => 'boolean',
        'driver' => 'boolean',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
        'is_wordpress_user' => 'boolean',
        'active' => 'boolean',
        'driver'=> 'boolean',
        'department_id' => 'integer',
        'publication_id',
        
       
    ];
    protected $dates = [
        'deleted_at'
    ];
    protected $attributes = [
    'registered' => 0,
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    
    public function status()
    {
    return $this->belongsTo(UserStatus::class, 'status_id');
    }

    public function sendNewUserNotification($password)
    {
        $this->notify(new NewUserNotification($this, $password));
    }
    public function sendRoleUpdatedNotification($oldRoles, $newRoles)
    {
        $this->notify(new RoleUpdatedNotification($this, $oldRoles, $newRoles));
    }
    
    public function isActive()
    {
        return $this->status && $this->status->label === 'Active';
    }
    public function isDriver()
    {
        return $this->driver == 1;
    }
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }
    public function isWordpressUser(){
        return $this->is_wordpress_user == 1;
    }
    public function changePassword()
    {
        return $this->change_password == 1;
    }
    public function canAccessModule($moduleName)
    {
        // Assuming you have a many-to-many relationship between users and modules
        return $this->modules()->where('name', $moduleName)->exists();
    }
    public function password()
    {
        return $this->password;
    }
    
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

//     public function department()
// {
//     return $this->belongsTo(Department::class);
// }

// public function publications()
// {
//     return $this->belongsToMany(Publication::class);
// }

// public function publications()
// {
//     return $this->belongsToMany(
//         Publication::class,
//         'publication_user',
//         'user_id',
//         'publication_id'
//     )->withTimestamps();
// }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Publications (pivot)
    public function publications()
    {
        return $this->belongsToMany(
            Publication::class,
            'publication_user',
            'user_id',
            'publication_id'
        )->withTimestamps();
    }

    


}
