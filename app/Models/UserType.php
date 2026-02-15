<?php

namespace App\Models;

class UserType extends MyBaseModel
{
    protected $fillable = ['name', 'slug', 'event_id', 'show_on_landing'];

    protected $casts = [
        'show_on_landing' => 'boolean',
    ];

    /**
     * The rules to validate the model.
     *
     * @return array $rules
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'show_on_landing' => 'nullable|boolean',
        ];
    }

    /**
     * Registration users that have this user type.
     */
    public function registrationUsers()
    {
        return $this->belongsToMany(RegistrationUser::class, 'registration_user_user_type', 'user_type_id', 'registration_user_id');
    }

    /**
     * Sub-types/options for this user type (e.g. Delegate -> "Type A", "Type B").
     */
    public function options()
    {
        return $this->hasMany(UserTypeOption::class, 'user_type_id')->orderBy('sort_order');
    }

    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    public $messages = [
        'name.required' => 'You must provide a name for the user type.',
        'name.string' => 'The name must be a valid string.',
        'name.max' => 'The name cannot exceed 255 characters.',
    ];

    protected $perPage = 10;

    /**
     * The event associated with the user type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Number of registration users with this type.
     */
    public function getUsersCountAttribute()
    {
        return $this->registrationUsers()->count();
    }
}
