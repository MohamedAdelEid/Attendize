<?php

namespace App\Models;

class UserType extends MyBaseModel
{
    protected $fillable = ['name', 'event_id'];

    /**
     * The rules to validate the model.
     *
     * @return array $rules
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
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
}
