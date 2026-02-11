<?php

namespace App\Models;

use Illuminate\Support\Carbon;

class Registration extends MyBaseModel
{
    protected $fillable = [
        'name',
        'image',
        'status',
        'start_date',
        'end_date',
        'approval_status',
        'max_participants',
        'show_on_landing',
        'is_members_form',
    ];


    public function rules()
    {
        $format = config('attendize.default_datetime_format');
        $event = Event::find(request()->route('event_id'));

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'image' => 'nullable|image|max:10240',
            'status' => 'required|in:active,inactive',
            'max_participants' => 'required|integer|min:1',
            'start_date' => [
                'required',
                "date_format:{$format}",
                function ($attribute, $value, $fail) use ($event) {
                    if ($event) {
                        $startDate = Carbon::createFromFormat('Y-m-d H:i', $value);
                        $eventStart = Carbon::parse($event->start_date);
                        $eventEnd = Carbon::parse($event->end_date);

                        if ($startDate->lt($eventStart)) {
                            $fail("The {$attribute} must be on or after the event start date ({$eventStart->format('Y-m-d H:i')}).");
                        }

                        if ($startDate->gt($eventEnd)) {
                            $fail("The {$attribute} must be on or before the event end date ({$eventEnd->format('Y-m-d H:i')}).");
                        }
                    }
                },
            ],
            'end_date' => [
                'required',
                "date_format:{$format}",
                'after:start_date',
                function ($attribute, $value, $fail) use ($event) {
                    if ($event && $value >= $event->end_date) {
                        $fail("The {$attribute} must be on or before the event end date ({$event->end_date}).");
                    }
                },
            ],
            'approval_status' => 'required|in:automatic,manual',
        ];
    }

    public function messages()
    {
        return [
            'start_date.required' => 'The start date is required.',
            'start_date.date_format' => 'The start date does not match the format: yyyy-mm-dd hh:mm.',
            'start_date.after' => 'The start date must be after the end date.',
            'end_date.required' => 'The end date is required.',
            'end_date.date_format' => 'The end date does not match the format: yyyy-mm-dd hh:mm.',
            'approval_status.required' => 'The approval status is required.',
            'approval_status.in' => 'The approval status must be either automatic or manual.',
        ];
    }

    // $casts
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'show_on_landing' => 'boolean',
        'is_members_form' => 'boolean',
    ];

        /**
     * Parse start_date to a Carbon instance
     *
     * @param  string  $date  DateTime
     */
    public function setStartDateAttribute($date)
    {
        $format = config('attendize.default_datetime_format');

        if ($date instanceof Carbon) {
            $this->attributes['start_date'] = $date->format($format);
        } else {
            $this->attributes['start_date'] = Carbon::createFromFormat($format, $date);
        }
    }

    /**
     * Format start date from user preferences
     * @return String Formatted date
     */
    public function startDateFormatted()
    {
        return $this->start_date->format(config('attendize.default_datetime_format'));
    }

    /**
     * Parse end_date to a Carbon instance
     *
     * @param  string  $date  DateTime
     */
    public function setEndDateAttribute($date)
    {
        $format = config('attendize.default_datetime_format');

        if ($date instanceof Carbon) {
            $this->attributes['end_date'] = $date->format($format);
        } else {
            $this->attributes['end_date'] = Carbon::createFromFormat($format, $date);
        }
    }

    /**
     * Format end date from user preferences
     * @return String Formatted date
     */
    public function endDateFormatted()
    {
        return $this->end_date->format(config('attendize.default_datetime_format'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'registration_users')
            ->withTimestamps();
    }

    /**
     * Get the registration users for the registration.
     */
    public function registrationUsers()
    {
        return $this->hasMany(RegistrationUser::class);
    }

    /**
     * Get the dynamic form fields for the registration.
     */
    public function dynamicFormFields()
    {
        return $this->hasMany(DynamicFormField::class)->orderBy('sort_order');
    }

        /**
     * Get the active dynamic form fields for the registration.
     */
    public function activeFormFields()
    {
        return $this->dynamicFormFields()->where('is_active', true)->orderBy('sort_order');
    }

}
