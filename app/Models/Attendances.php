<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'registration_user_id',
        'event_id',
        'check_in',
        'check_out',
        'status',
    ];

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
