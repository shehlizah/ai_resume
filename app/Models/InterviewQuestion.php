<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewQuestion extends Model
{
    protected $fillable = [
        'session_id',
        'question_number',
        'question_text',
        'question_type',
        'focus_area',
        'answer_text',
        'score',
        'feedback',
        'answered_at',
    ];

    protected $casts = [
        'feedback' => 'array',
        'score' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(InterviewSession::class, 'session_id', 'session_id');
    }

    /**
     * Check if question has been answered
     */
    public function isAnswered(): bool
    {
        return !empty($this->answer_text);
    }

    /**
     * Get feedback as a formatted string
     */
    public function getFormattedFeedback(): string
    {
        if (empty($this->feedback)) {
            return 'No feedback available.';
        }

        $feedbackArray = is_array($this->feedback) ? $this->feedback : json_decode($this->feedback, true);

        $output = '';

        if (isset($feedbackArray['feedback'])) {
            $output .= $feedbackArray['feedback'] . "\n\n";
        }

        if (!empty($feedbackArray['strengths'])) {
            $output .= "Strengths:\n";
            foreach ($feedbackArray['strengths'] as $strength) {
                $output .= "• " . $strength . "\n";
            }
            $output .= "\n";
        }

        if (!empty($feedbackArray['improvements'])) {
            $output .= "Areas for Improvement:\n";
            foreach ($feedbackArray['improvements'] as $improvement) {
                $output .= "• " . $improvement . "\n";
            }
        }

        return trim($output);
    }
}
