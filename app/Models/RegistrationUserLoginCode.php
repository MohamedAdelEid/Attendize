<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RegistrationUserLoginCode extends Model
{
    protected $fillable = [
        'registration_user_id',
        'event_id',
        'code',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    public static function createForUser(RegistrationUser $user, int $eventId): self
    {
        static::where('registration_user_id', $user->id)
            ->where('event_id', $eventId)
            ->whereNull('used_at')
            ->delete();

        return static::create([
            'registration_user_id' => $user->id,
            'event_id' => $eventId,
            'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);
    }
}
