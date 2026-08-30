<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AbstractReviewer extends Authenticatable
{
    use Notifiable;

    protected $table = 'abstract_reviewers';

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'password',
        'is_active',
        'access_all_abstracts',
        'can_review',
        'can_edit',
        'can_delete',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'access_all_abstracts' => 'boolean',
        'can_review' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function abstracts()
    {
        return $this->belongsToMany(EventAbstract::class, 'abstract_reviewer_abstract', 'abstract_reviewer_id', 'abstract_id')
            ->withTimestamps();
    }

    public function reviewedSubmissions()
    {
        return $this->hasMany(AbstractSubmission::class, 'reviewed_by_reviewer_id');
    }

    /**
     * Abstract campaign IDs this reviewer may access (null = all for event).
     */
    public function accessibleAbstractIds(): ?array
    {
        if ($this->access_all_abstracts) {
            return null;
        }

        return $this->abstracts()->pluck('abstracts.id')->all();
    }

    public function canAccessAbstract(int $abstractId): bool
    {
        if ($this->access_all_abstracts) {
            return EventAbstract::where('event_id', $this->event_id)->where('id', $abstractId)->exists();
        }

        return $this->abstracts()->where('abstracts.id', $abstractId)->exists();
    }

    /**
     * Base query for submissions within this reviewer's scope.
     */
    public function submissionsQuery()
    {
        $query = AbstractSubmission::whereHas('abstractDefinition', function ($q) {
            $q->where('event_id', $this->event_id);
        });

        $ids = $this->accessibleAbstractIds();
        if ($ids !== null) {
            if (empty($ids)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('abstract_id', $ids);
            }
        }

        return $query;
    }
}
