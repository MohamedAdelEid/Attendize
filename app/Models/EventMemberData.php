<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMemberData extends Model
{
    protected $table = 'event_member_data';

    protected $fillable = ['event_member_id', 'field_key', 'value'];

    public function eventMember()
    {
        return $this->belongsTo(EventMember::class);
    }
}
