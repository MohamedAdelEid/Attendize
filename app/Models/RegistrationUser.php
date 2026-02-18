<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationUser extends Model
{
    protected $table = 'registration_users';

    protected $fillable = [
        'registration_id',
        'category_id',
        'profession_id',
        'conference_id',
        'user_id',
        'first_name',
        'last_name',
        'title',
        'bio',
        'email',
        'phone',
        'avatar',
        'status',
        'unique_code',
        'qr_code_path',
        'is_new',
        'ticket_token',
        'check_in',
        'check_out',
    ];

    protected $casts = [
        'is_new' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendances::class);
    }
    /**
     * Get the registration that owns the registration user.
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Get the user that owns the registration user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the registration user.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the conference that owns the registration user.
     */
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get the profession that owns the registration user.
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get the dynamic form field values for the registration user.
     */
    public function formFieldValues()
    {
        return $this->hasMany(DynamicFormFieldValue::class, 'registration_user_id');
    }

    /**
     * Get the user types for this registration user (many-to-many), with optional sub-type (option) per type.
     */
    public function userTypes()
    {
        return $this->belongsToMany(UserType::class, 'registration_user_user_type', 'registration_user_id', 'user_type_id')
            ->withPivot('user_type_option_id', 'position');
    }

    /**
     * First user type for backward compatibility (e.g. $user->userType).
     */
    public function getUserTypeAttribute()
    {
        return $this->userTypes->first();
    }

    /**
     * Get all check-in/check-out logs for the registration user.
     */
    public function checkInCheckOutLogs()
    {
        return $this->hasMany(CheckInCheckOutLog::class);
    }

    /**
     * Get all payments for the registration user.
     */
    public function payments()
    {
        return $this->hasMany(RegistrationPayment::class);
    }

    /**
     * Member-specific data (when user is in a "Member" category).
     */
    public function memberData()
    {
        return $this->hasMany(RegistrationUserMemberData::class);
    }
}
