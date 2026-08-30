<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AbstractReviewerLoginToken extends Model
{
    protected $fillable = [
        'abstract_reviewer_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(AbstractReviewer::class, 'abstract_reviewer_id');
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    public static function createForReviewer(AbstractReviewer $reviewer, int $hoursValid = 48): self
    {
        return static::create([
            'abstract_reviewer_id' => $reviewer->id,
            'token' => Str::random(48),
            'expires_at' => Carbon::now()->addHours($hoursValid),
        ]);
    }
}
