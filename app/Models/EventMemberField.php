<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMemberField extends Model
{
    protected $table = 'event_member_fields';

    protected $fillable = [
        'event_id',
        'field_key',
        'label',
        'type',
        'is_required',
        'is_unique',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
