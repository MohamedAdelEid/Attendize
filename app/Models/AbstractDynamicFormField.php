<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractDynamicFormField extends Model
{
    protected $table = 'abstract_dynamic_form_fields';

    protected $fillable = [
        'abstract_id',
        'label',
        'description',
        'name',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function abstractDefinition()
    {
        return $this->belongsTo(EventAbstract::class, 'abstract_id');
    }

    public function values()
    {
        return $this->hasMany(AbstractDynamicFormFieldValue::class, 'abstract_dynamic_form_field_id');
    }

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
