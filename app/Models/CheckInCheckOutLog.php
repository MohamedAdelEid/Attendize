<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInCheckOutLog extends Model
{
    protected $table = 'check_in_check_out_logs';

    protected $fillable = [
        'registration_user_id',
        'event_id',
        'action',
        'action_time',
    ];

    protected $casts = [
        'action_time' => 'datetime',
    ];

    /**
     * Get the registration user that owns the log.
     */
    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }

    /**
     * Get the event that owns the log.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}