<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractTemplate extends Model
{
    protected $table = 'abstract_templates';

    protected $fillable = [
        'abstract_id',
        'abstract_category_id',
        'template_path',
        'sort_order',
    ];

    public function abstractDefinition()
    {
        return $this->belongsTo(EventAbstract::class, 'abstract_id');
    }

    public function category()
    {
        return $this->belongsTo(AbstractCategory::class, 'abstract_category_id');
    }

    public function getTemplateUrlAttribute()
    {
        if (!$this->template_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->template_path, '/'));
    }
}
