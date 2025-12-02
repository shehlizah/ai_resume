<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'job_title',
        'company',
        'interview_type',
        'status',
        'overall_score',
        'final_summary',
        'final_report',
        'total_questions',
        'completed_at',
    ];

    protected $casts = [
        'final_report' => 'array',
        'overall_score' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(InterviewQuestion::class, 'session_id', 'session_id')
            ->orderBy('question_number');
    }

    /**
     * Mark session as completed and calculate overall score
     */
    public function complete(): void
    {
        $questions = $this->questions()->whereNotNull('score')->get();

        if ($questions->count() > 0) {
            $averageScore = $questions->avg('score');

            $this->update([
                'status' => 'completed',
                'overall_score' => round($averageScore, 2),
                'total_questions' => $questions->count(),
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Get session progress percentage
     */
    public function getProgressAttribute(): int
    {
        $answeredCount = $this->questions()->whereNotNull('answer_text')->count();
        $totalCount = $this->questions()->count();

        return $totalCount > 0 ? round(($answeredCount / $totalCount) * 100) : 0;
    }
}
