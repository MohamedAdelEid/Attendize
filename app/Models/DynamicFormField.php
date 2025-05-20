<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicFormField extends Model
{
    protected $fillable = [
        'registration_id',
        'label',
        'name',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the registration that owns the field.
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Get the field values for this field.
     */
    public function values()
    {
        return $this->hasMany(DynamicFormFieldValue::class);
    }

    /**
     * Get the available field types.
     */
    public static function getFieldTypes()
    {
        return [
            'text' => 'Text',
            'email' => 'Email',
            'number' => 'Number',
            'tel' => 'Telephone',
            'date' => 'Date',
            'time' => 'Time',
            'datetime-local' => 'Date & Time',
            'url' => 'URL',
            'textarea' => 'Text Area',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button',
            'file' => 'File Upload',
            'country' => 'Country',
            'city' => 'City',
        ];
    }
}