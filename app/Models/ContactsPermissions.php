<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactsPermissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'contacts_id',
        'last_seen',
        'live_location',
        'location_history',
        'removed',
    ];

    protected $hidden = [
        'removed',
    ];

    public function contacts()
    {
        return $this->belongsTo(Contacts::class);
    }
}
