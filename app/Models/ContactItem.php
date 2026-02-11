<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactItem extends Model
{
    use SoftDeletes;

    protected $table = 'contact_items';

    public $timestamps = false;

    protected $fillable = [
        'contact_id',
        'type',
        'value',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

}
