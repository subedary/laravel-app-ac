<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];


    public function parent()
    {
        return $this->belongsTo(Publication::class, 'parent_id');
    }


    // Users associated with this publication
    // public function users()
    // {
    //     return $this->belongsToMany(User::class);
    // }
 
public function users(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'publication_user',
        'publication_id',
        'user_id'
    )->withTimestamps();
}

}
