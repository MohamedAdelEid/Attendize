<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AbstractCategory extends Model
{
    protected $table = 'abstract_categories';

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function abstracts()
    {
        return $this->hasManyThrough(
            EventAbstract::class,
            AbstractTemplate::class,
            'abstract_category_id',
            'id',
            'id',
            'abstract_id'
        );
    }

    public function templates()
    {
        return $this->hasMany(AbstractTemplate::class, 'abstract_category_id');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}
