<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\User;        
use App\Models\Department; 

class Role extends SpatieRole
{
    use HasFactory; 
      
    protected $fillable = [
        'name',
        'guard_name', 
         'department_id',
    ];

     public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    
}
