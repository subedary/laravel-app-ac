<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
    ];


    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }


    // Users in this department
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
