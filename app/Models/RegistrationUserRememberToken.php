<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegistrationUserRememberToken extends Model
{
    protected $fillable = [
        'registration_user_id',
        'event_id',
        'token',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    public static function createForUser(RegistrationUser $user, int $eventId, int $days = 30): self
    {
        return static::create([
            'registration_user_id' => $user->id,
            'event_id' => $eventId,
            'token' => Str::random(48),
            'expires_at' => Carbon::now()->addDays($days),
        ]);
    }
}
