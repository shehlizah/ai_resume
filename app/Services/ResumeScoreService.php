<?php

namespace App\Services;

class ResumeScoreService
{
    /**
     * Calculate resume score based on completeness and quality
     *
     * @param array $data Resume data
     * @return array ['score' => int, 'feedback' => array, 'suggestions' => array]
     */
    public function calculateScore(array $data): array
    {
        $score = 0;
        $maxScore = 100;
        $feedback = [];
        $suggestions = [];

        // 1. Basic Information (20 points)
        $basicScore = $this->scoreBasicInfo($data);
        $score += $basicScore['score'];
        $feedback['basic_info'] = $basicScore['feedback'];
        if ($basicScore['score'] < 20) {
            $suggestions[] = 'Complete all basic information fields (name, title, email, phone, address)';
        }

        // 2. Professional Summary (15 points)
        $summaryScore = $this->scoreSummary($data);
        $score += $summaryScore['score'];
        $feedback['summary'] = $summaryScore['feedback'];
        if ($summaryScore['score'] < 15) {
            $suggestions[] = 'Add a compelling professional summary (150-250 words recommended)';
        }

        // 3. Work Experience (30 points)
        $experienceScore = $this->scoreExperience($data);
        $score += $experienceScore['score'];
        $feedback['experience'] = $experienceScore['feedback'];
        if ($experienceScore['score'] < 25) {
            $suggestions[] = 'Add detailed work experience with responsibilities and achievements';
        }

        // 4. Education (15 points)
        $educationScore = $this->scoreEducation($data);
        $score += $educationScore['score'];
        $feedback['education'] = $educationScore['feedback'];
        if ($educationScore['score'] < 12) {
            $suggestions[] = 'Include your educational background with institution and year';
        }

        // 5. Skills (15 points)
        $skillsScore = $this->scoreSkills($data);
        $score += $skillsScore['score'];
        $feedback['skills'] = $skillsScore['feedback'];
        if ($skillsScore['score'] < 12) {
            $suggestions[] = 'List relevant skills and competencies';
        }

        // 6. Formatting & Completeness (5 points)
        $formatScore = $this->scoreFormatting($data);
        $score += $formatScore['score'];
        $feedback['formatting'] = $formatScore['feedback'];

        return [
            'score' => min($score, $maxScore),
            'feedback' => $feedback,
            'suggestions' => $suggestions,
            'grade' => $this->getGrade($score)
        ];
    }

    private function scoreBasicInfo(array $data): array
    {
        $score = 0;
        $feedback = [];

        if (!empty($data['name'])) {
            $score += 5;
            $feedback[] = '✓ Name provided';
        }
        if (!empty($data['title'])) {
            $score += 5;
            $feedback[] = '✓ Job title provided';
        }
        if (!empty($data['email'])) {
            $score += 3;
            $feedback[] = '✓ Email provided';
        }
        if (!empty($data['phone'])) {
            $score += 3;
            $feedback[] = '✓ Phone provided';
        }
        if (!empty($data['address'])) {
            $score += 4;
            $feedback[] = '✓ Address provided';
        }

        return ['score' => $score, 'feedback' => implode(', ', $feedback)];
    }

    private function scoreSummary(array $data): array
    {
        $score = 0;
        $feedback = '';

        if (!empty($data['summary'])) {
            $wordCount = str_word_count($data['summary']);

            if ($wordCount >= 150 && $wordCount <= 300) {
                $score = 15;
                $feedback = "✓ Excellent summary ({$wordCount} words)";
            } elseif ($wordCount >= 100) {
                $score = 12;
                $feedback = "✓ Good summary ({$wordCount} words)";
            } elseif ($wordCount >= 50) {
                $score = 8;
                $feedback = "⚠ Summary could be more detailed ({$wordCount} words)";
            } else {
                $score = 4;
                $feedback = "⚠ Summary is too brief ({$wordCount} words)";
            }
        } else {
            $feedback = '✗ No professional summary provided';
        }

        return ['score' => $score, 'feedback' => $feedback];
    }

    private function scoreExperience(array $data): array
    {
        $score = 0;
        $feedback = '';

        // Check for structured experience data
        $experienceCount = 0;
        if (!empty($data['job_title']) && is_array($data['job_title'])) {
            $experienceCount = count(array_filter($data['job_title']));
        }

        if ($experienceCount >= 3) {
            $score = 30;
            $feedback = "✓ Excellent work history ({$experienceCount} positions)";
        } elseif ($experienceCount >= 2) {
            $score = 22;
            $feedback = "✓ Good work history ({$experienceCount} positions)";
        } elseif ($experienceCount >= 1) {
            $score = 15;
            $feedback = "⚠ Add more work experience entries";
        } else {
            $feedback = '✗ No work experience provided';
        }

        // Bonus for detailed responsibilities
        if (!empty($data['responsibilities']) && is_array($data['responsibilities'])) {
            $detailedCount = 0;
            foreach ($data['responsibilities'] as $resp) {
                if (!empty($resp) && strlen($resp) > 50) {
                    $detailedCount++;
                }
            }
            if ($detailedCount > 0) {
                $score = min($score + 5, 30);
            }
        }

        return ['score' => $score, 'feedback' => $feedback];
    }

    private function scoreEducation(array $data): array
    {
        $score = 0;
        $feedback = '';

        $educationCount = 0;
        if (!empty($data['degree']) && is_array($data['degree'])) {
            $educationCount = count(array_filter($data['degree']));
        }

        if ($educationCount >= 2) {
            $score = 15;
            $feedback = "✓ Excellent educational background ({$educationCount} degrees)";
        } elseif ($educationCount >= 1) {
            $score = 12;
            $feedback = "✓ Education included";
        } else {
            $feedback = '✗ No education provided';
        }

        return ['score' => $score, 'feedback' => $feedback];
    }

    private function scoreSkills(array $data): array
    {
        $score = 0;
        $feedback = '';

        if (!empty($data['skills'])) {
            $skillsText = is_string($data['skills']) ? $data['skills'] : '';
            $skillCount = substr_count($skillsText, "\n") + 1;

            if ($skillCount >= 10) {
                $score = 15;
                $feedback = "✓ Comprehensive skills list ({$skillCount} skills)";
            } elseif ($skillCount >= 6) {
                $score = 12;
                $feedback = "✓ Good skills list ({$skillCount} skills)";
            } elseif ($skillCount >= 3) {
                $score = 8;
                $feedback = "⚠ Add more skills ({$skillCount} listed)";
            } else {
                $score = 4;
                $feedback = "⚠ Skills section needs expansion";
            }
        } else {
            $feedback = '✗ No skills provided';
        }

        return ['score' => $score, 'feedback' => $feedback];
    }

    private function scoreFormatting(array $data): array
    {
        $score = 5; // Default - assume good formatting
        $feedback = '✓ Resume is well-structured';

        return ['score' => $score, 'feedback' => $feedback];
    }

    private function getGrade(int $score): string
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 80) return 'Very Good';
        if ($score >= 70) return 'Good';
        if ($score >= 60) return 'Fair';
        return 'Room for Improvement';
    }

    /**
     * Get detailed feedback based on user's subscription package
     *
     * @param string $packageType 'basic', 'pro', 'premium'
     * @param array $scoreData Score data from calculateScore()
     * @return array Feedback visible to user based on their package
     */
    public function getPackageBasedFeedback(string $packageType, array $scoreData): array
    {
        $result = [
            'score' => $scoreData['score'],
            'grade' => $scoreData['grade'],
            'feedback' => null,
            'suggestions' => null
        ];

        switch (strtolower($packageType)) {
            case 'basic':
                // Basic: Only score and grade
                break;

            case 'pro':
                // Pro: Score + basic feedback
                $result['feedback'] = [
                    'summary' => "Your resume scored {$scoreData['score']}/100 - {$scoreData['grade']}",
                    'sections' => [
                        'Basic Info' => $scoreData['feedback']['basic_info'] ?? '',
                        'Summary' => $scoreData['feedback']['summary'] ?? '',
                        'Experience' => $scoreData['feedback']['experience'] ?? '',
                    ]
                ];
                break;

            case 'premium':
                // Premium: Full feedback + actionable suggestions
                $result['feedback'] = [
                    'summary' => "Your resume scored {$scoreData['score']}/100 - {$scoreData['grade']}",
                    'sections' => $scoreData['feedback']
                ];
                $result['suggestions'] = $scoreData['suggestions'];
                break;
        }

        return $result;
    }
}
