<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertMessages extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender',
        'receiver',
        'message',
        'location_name',
        'latitude',
        'longitude',
        'message',
        'email_sent',
        'text_message_sent',
        'removed',
    ];
}
