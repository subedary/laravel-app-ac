<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $dates = ['deleted_at', 'added_timestamp'];

    public $timestamps = false; 

    protected $fillable = [
        'name', 'phone',

        // Contact
        'contact_name', 'contact_email_nickname', 'contact_email',
        'contact_phone_office', 'contact_phone_mobile',
        'contact_preference',

        // Billing Contact
        'billing_contact_name', 
        'billing_contact_email_nickname',
        'billing_contact_email', 
        'billing_contact_phone_office',
        'billing_contact_phone_phone', 
        'billing_preference',

        // Address
        'address_line1', 
        'address_line2', 
        'address_city',
        'address_state', 
        'address_zipcode', 
        'address_country',

        // Additional metadata
        'added_timestamp',
        'type', 
        'innline_types', 
        'innline_amenities',

        // Status flags
        'open', 'active',

        // Assigned user relationships
        'primary_contact_id', 
        'primary_ad_rep_id', 
        'secondary_ad_rep_id',

        // Website
        'website_part', 
        'website',

        // Misc
        'wisconsin_resale_number',
        'owner_alumni_school_district',

        // Newsletters
        'newsletter_weekly_business_updates',
        'newsletter_pulse_picks',

        // Marketing
        'marketing_preferences',
        'marketing_contact_name',
        'marketing_contact_email_nickname',
        'marketing_contact_email',
        'marketing_contact_phone_office',
        'marketing_contact_phone_mobile',
        'marketing_contact_preference',
    ];
    // UPDATE (inline + full update)
    public function updateClient(Request $request, Client $client)
    {
        $client->update($request->only($client->getFillable()));

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }
    public function primaryContact()
{
    return $this->belongsTo(User::class, 'primary_contact_id');
}

public function primaryAdRep()
{
    return $this->belongsTo(User::class, 'primary_ad_rep_id');
}

public function secondaryAdRep()
{
    return $this->belongsTo(User::class, 'secondary_ad_rep_id');
}

}
