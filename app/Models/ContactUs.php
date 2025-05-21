<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $table = 'contact_us';

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'subject',
        'message',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
