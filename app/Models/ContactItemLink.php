<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactItemLink extends Model
{
    protected $table = 'contacts_items_links';

    protected $fillable = [
        'contact_id',
        'item_id',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function contactItem(): BelongsTo
    {
        return $this->belongsTo(ContactItem::class, 'item_id');
    }
}
