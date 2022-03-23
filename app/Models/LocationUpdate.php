<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationUpdate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'location_history_id',
        'latitude',
        'longitude',
        'location_name',
        'removed',
    ];

    public function locationHistory()
    {
        return $this->belongsTo(LocationHistory::class);
    }
}
