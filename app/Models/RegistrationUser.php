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
        'email',
        'phone',
        'status',
        'unique_code',
        'qr_code_path',
        'is_new',
        'ticket_token',
        'check_in',
        'check_out',
        'user_type_id',
    ];

    protected $casts = [
        'is_new' => 'boolean',
    ];

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
     * Get the user type that owns the registration user.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
