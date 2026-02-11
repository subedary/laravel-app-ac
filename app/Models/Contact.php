<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'client_id',
        'notes',
    ];


    public function contactItems(): HasMany
    {
        return $this->hasMany(ContactItem::class, 'contact_id', 'id');
    }

}
