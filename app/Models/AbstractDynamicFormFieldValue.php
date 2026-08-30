<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractDynamicFormFieldValue extends Model
{
    protected $table = 'abstract_dynamic_form_field_values';

    protected $fillable = [
        'abstract_submission_id',
        'abstract_dynamic_form_field_id',
        'value',
    ];

    public function submission()
    {
        return $this->belongsTo(AbstractSubmission::class, 'abstract_submission_id');
    }

    public function field()
    {
        return $this->belongsTo(AbstractDynamicFormField::class, 'abstract_dynamic_form_field_id');
    }
}
