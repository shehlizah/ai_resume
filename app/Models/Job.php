<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\MatchCandidatesJob;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'user_id',
        'title',
        'company',
        'location',
        'type',
        'description',
        'salary',
        'tags',
        'posted_at',
        'source',
        'url',
        'is_featured',
        'is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'posted_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected static function booted()
    {
        static::created(function ($job) {
            // Only trigger matching for company-posted jobs
            if ($job->source === 'company' && $job->user_id) {
                // Check if employer has AI matching add-on
                $employer = $job->user;
                if ($employer && $employer->isEmployer()) {
                    $hasAccess = $employer->activeEmployerAddOns()
                        ->whereHas('addOn', fn($q) => $q->where('type', 'ai_matching'))
                        ->exists();

                    if ($hasAccess) {
                        // Dispatch with 30-minute delay for 30-minute SLA
                        MatchCandidatesJob::dispatch($job)->delay(now()->addMinutes(30));
                    }
                }
            }
        });
    }

    public function getTagsArrayAttribute()
    {
        return is_string($this->tags) ? json_decode($this->tags, true) : $this->tags;
    }

    public function getTimeAgoAttribute()
    {
        return $this->posted_at->diffForHumans();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('posted_at', 'desc');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function candidateMatches()
    {
        return $this->hasMany(JobCandidateMatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
