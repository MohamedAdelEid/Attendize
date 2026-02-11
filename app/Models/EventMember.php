<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    protected $table = 'event_members';

    protected $fillable = ['event_id', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function data()
    {
        return $this->hasMany(EventMemberData::class)->orderBy('id');
    }

    /**
     * Get value for a field key (from event_member_data).
     */
    public function getValue(string $fieldKey): ?string
    {
        $d = $this->data->where('field_key', $fieldKey)->first();
        return $d ? $d->value : null;
    }

    /**
     * Get all data as key => value.
     */
    public function getDataByKey(): \Illuminate\Support\Collection
    {
        return $this->data->pluck('value', 'field_key');
    }
}
