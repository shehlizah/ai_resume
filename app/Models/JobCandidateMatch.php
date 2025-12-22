<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCandidateMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'user_resume_id',
        'match_score',
        'match_details',
        'ai_summary',
        'status',
        'matched_at',
    ];

    protected $casts = [
        'match_details' => 'array',
        'matched_at' => 'datetime',
        'match_score' => 'integer',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resume()
    {
        return $this->belongsTo(UserResume::class, 'user_resume_id');
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighScore($query, $threshold = 70)
    {
        return $query->where('match_score', '>=', $threshold);
    }

    public function getMatchPercentageAttribute()
    {
        return min(100, max(0, $this->match_score)) . '%';
    }

    public function getScoreColorAttribute()
    {
        if ($this->match_score >= 80) return 'success';
        if ($this->match_score >= 60) return 'warning';
        return 'danger';
    }
}
