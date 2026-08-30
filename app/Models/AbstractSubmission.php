<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractSubmission extends Model
{
    protected $table = 'abstract_submissions';

    protected $fillable = [
        'abstract_id',
        'abstract_category_id',
        'registration_user_id',
        'full_name',
        'email',
        'phone',
        'authors',
        'details',
        'domain',
        'file_path',
        'final_file_path',
        'final_submitted_at',
        'status',
        'reviewed_at',
        'review_notes',
        'reviewed_by_reviewer_id',
        'submitted_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'final_submitted_at' => 'datetime',
    ];

    public function abstractDefinition()
    {
        return $this->belongsTo(EventAbstract::class, 'abstract_id');
    }

    public function category()
    {
        return $this->belongsTo(AbstractCategory::class, 'abstract_category_id');
    }

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class, 'registration_user_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(AbstractReviewer::class, 'reviewed_by_reviewer_id');
    }

    public function formFieldValues()
    {
        return $this->hasMany(AbstractDynamicFormFieldValue::class, 'abstract_submission_id');
    }

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function getFinalFileUrlAttribute()
    {
        if (!$this->final_file_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->final_file_path, '/'));
    }

    public function needsFinalUpload(): bool
    {
        return $this->status === 'approved' && !$this->final_file_path;
    }

    public function getDisplayNameAttribute()
    {
        if ($this->full_name) {
            return $this->full_name;
        }

        if ($this->registrationUser) {
            return trim($this->registrationUser->first_name . ' ' . $this->registrationUser->last_name);
        }

        return $this->email ?: '—';
    }
}
