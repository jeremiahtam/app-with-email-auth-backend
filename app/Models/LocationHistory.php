<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationHistory extends Model
{
    use HasFactory;

    protected $hidden = [
        'removed',
    ];
    
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'location_name',
        'live',
        'removed',
    ];

    public function locationUpdate()
    {
        return $this->hasMany(LocationUpdate::class);
    }
}
