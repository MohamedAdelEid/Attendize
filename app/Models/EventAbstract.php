<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Event abstract campaign / form definition.
 * Named EventAbstract because "Abstract" is a reserved PHP keyword.
 */
class EventAbstract extends Model
{
    protected $table = 'abstracts';

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'instructions',
        'max_submissions_per_user',
        'register_condition',
        'all_event_registrations',
        'approval_mode',
        'email_subject',
        'email_body',
        'email_attach_template',
        'status',
        'start_date',
        'end_date',
        'show_on_landing',
    ];

    protected $casts = [
        'all_event_registrations' => 'boolean',
        'email_attach_template' => 'boolean',
        'show_on_landing' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_submissions_per_user' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function templates()
    {
        return $this->hasMany(AbstractTemplate::class, 'abstract_id')->orderBy('sort_order');
    }

    public function dynamicFormFields()
    {
        return $this->hasMany(AbstractDynamicFormField::class, 'abstract_id')->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(AbstractSubmission::class, 'abstract_id');
    }

    public function reviewers()
    {
        return $this->belongsToMany(AbstractReviewer::class, 'abstract_reviewer_abstract', 'abstract_id', 'abstract_reviewer_id')
            ->withTimestamps();
    }

    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'abstract_registration', 'abstract_id', 'registration_id')
            ->withTimestamps();
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function isRegisteredOnly(): bool
    {
        return $this->register_condition === 'registered_only';
    }

    public function getPublicUrlAttribute()
    {
        if (!$this->event) {
            return null;
        }

        return route('showEventAbstractForm', [
            'event_id' => $this->event_id,
            'event_slug' => Str::slug($this->event->title),
            'slug' => $this->slug,
        ]);
    }

    public function generateUniqueSlug(string $name, int $eventId, ?int $excludeId = null): string
    {
        $base = Str::slug($name) ?: 'abstract';
        $slug = $base;
        $i = 1;

        while (
            static::where('event_id', $eventId)
                ->where('slug', $slug)
                ->when($excludeId, function ($q) use ($excludeId) {
                    $q->where('id', '!=', $excludeId);
                })
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function allowedCategoryIds(): array
    {
        return $this->templates()->pluck('abstract_category_id')->filter()->all();
    }
}
