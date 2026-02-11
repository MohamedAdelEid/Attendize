<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMemberFieldMapping extends Model
{
    protected $table = 'event_member_field_mappings';

    protected $fillable = [
        'event_id',
        'registration_id',
        'member_field_key',
        'target_type',
        'target_dynamic_form_field_id',
    ];

    public const TARGET_FIRST_NAME = 'first_name';
    public const TARGET_LAST_NAME = 'last_name';
    public const TARGET_EMAIL = 'email';
    public const TARGET_PHONE = 'phone';
    public const TARGET_DYNAMIC_FIELD = 'dynamic_field';

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function targetDynamicFormField()
    {
        return $this->belongsTo(DynamicFormField::class, 'target_dynamic_form_field_id');
    }
}
