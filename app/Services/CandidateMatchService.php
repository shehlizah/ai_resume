<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
use App\Models\UserResume;
use App\Models\JobCandidateMatch;
use Illuminate\Support\Facades\Log;

class CandidateMatchService
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService = null)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Find and score candidates for a job
     */
    public function matchCandidatesForJob(Job $job, int $limit = 50)
    {
        $jobKeywords = $this->extractJobKeywords($job);
        $jobSkills = $this->extractJobSkills($job);

        // Get candidates with completed resumes who are available
        $candidates = User::where('role', 'user')
            ->where('is_active', true)
            ->whereHas('resumes', function ($q) {
                $q->where('status', 'completed');
            })
            ->with(['resumes' => function ($q) {
                $q->where('status', 'completed')->latest();
            }])
            ->limit(200) // Pre-filter to avoid processing too many
            ->get();

        $matches = [];

        foreach ($candidates as $candidate) {
            $resume = $candidate->resumes->first();
            if (!$resume) continue;

            $score = $this->calculateMatchScore($job, $jobKeywords, $jobSkills, $candidate, $resume);

            if ($score >= 50) { // Only store matches above 50%
                $matches[] = [
                    'job_id' => $job->id,
                    'user_id' => $candidate->id,
                    'user_resume_id' => $resume->id,
                    'match_score' => $score,
                    'match_details' => $this->buildMatchDetails($job, $jobKeywords, $jobSkills, $resume),
                    'status' => $score >= 75 ? 'shortlisted' : 'pending',
                    'matched_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Sort by score and take top matches
        usort($matches, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        $matches = array_slice($matches, 0, $limit);

        // Optionally enhance with AI summaries for top matches
        if ($this->openAIService && count($matches) > 0) {
            $matches = $this->enhanceWithAISummaries($job, $matches, 10);
        }

        // Bulk insert
        if (!empty($matches)) {
            JobCandidateMatch::insert($matches);
        }

        return count($matches);
    }

    /**
     * Extract keywords from job description and title
     */
    protected function extractJobKeywords(Job $job): array
    {
        $text = strtolower($job->title . ' ' . $job->description);

        // Remove common words
        $stopWords = ['the', 'and', 'for', 'with', 'you', 'are', 'this', 'that', 'from', 'will', 'can', 'our', 'your', 'have', 'has'];

        $words = preg_split('/\W+/', $text);
        $words = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopWords));

        return array_values(array_unique($words));
    }

    /**
     * Extract skills from job tags and description
     */
    protected function extractJobSkills(Job $job): array
    {
        $skills = [];

        // From tags
        if ($job->tags && is_array($job->tags)) {
            $skills = array_merge($skills, array_map('strtolower', $job->tags));
        }

        // Common tech skills from description
        $techSkills = ['php', 'laravel', 'javascript', 'react', 'vue', 'python', 'java', 'node', 'sql',
                       'mysql', 'postgresql', 'mongodb', 'aws', 'docker', 'kubernetes', 'git', 'api',
                       'html', 'css', 'typescript', 'angular', 'django', 'flask', 'spring'];

        $description = strtolower($job->description ?? '');
        foreach ($techSkills as $skill) {
            if (str_contains($description, $skill)) {
                $skills[] = $skill;
            }
        }

        return array_values(array_unique($skills));
    }

    /**
     * Calculate match score between job and candidate
     */
    protected function calculateMatchScore(Job $job, array $jobKeywords, array $jobSkills, User $candidate, UserResume $resume): int
    {
        $score = 0;
        $resumeData = $resume->data ?? [];

        // Extract resume text
        $resumeText = strtolower(json_encode($resumeData));

        // Skills match (40% weight)
        $skillsMatch = 0;
        if (!empty($jobSkills)) {
            foreach ($jobSkills as $skill) {
                if (str_contains($resumeText, strtolower($skill))) {
                    $skillsMatch++;
                }
            }
            $skillsScore = (count($jobSkills) > 0) ? ($skillsMatch / count($jobSkills)) * 40 : 0;
            $score += $skillsScore;
        }

        // Keywords match (30% weight)
        $keywordMatch = 0;
        $sampleKeywords = array_slice($jobKeywords, 0, 20); // Check top 20 keywords
        if (!empty($sampleKeywords)) {
            foreach ($sampleKeywords as $keyword) {
                if (str_contains($resumeText, $keyword)) {
                    $keywordMatch++;
                }
            }
            $keywordScore = (count($sampleKeywords) > 0) ? ($keywordMatch / count($sampleKeywords)) * 30 : 0;
            $score += $keywordScore;
        }

        // Experience level (20% weight)
        $experienceScore = $this->scoreExperience($job, $resumeData);
        $score += $experienceScore * 20;

        // Location match (10% weight) - simple check
        $locationScore = 0;
        $jobLocation = strtolower($job->location ?? '');
        if (str_contains($jobLocation, 'remote') || str_contains($resumeText, strtolower($job->location))) {
            $locationScore = 1;
        }
        $score += $locationScore * 10;

        return (int) min(100, max(0, $score));
    }

    /**
     * Score experience level match
     */
    protected function scoreExperience(Job $job, array $resumeData): float
    {
        // Count years of experience from resume
        $experiences = $resumeData['job_title'] ?? [];
        if (empty($experiences) || !is_array($experiences)) {
            return 0.3; // Some baseline score
        }

        $yearCount = count($experiences);

        // Simple heuristic based on job title
        $jobTitle = strtolower($job->title);
        if (str_contains($jobTitle, 'senior') || str_contains($jobTitle, 'lead')) {
            return $yearCount >= 4 ? 1 : ($yearCount >= 2 ? 0.6 : 0.3);
        } elseif (str_contains($jobTitle, 'junior') || str_contains($jobTitle, 'entry')) {
            return $yearCount <= 2 ? 1 : 0.7;
        }

        // Mid-level
        return $yearCount >= 2 ? 1 : 0.6;
    }

    /**
     * Build detailed match breakdown
     */
    protected function buildMatchDetails(Job $job, array $jobKeywords, array $jobSkills, UserResume $resume): array
    {
        $resumeText = strtolower(json_encode($resume->data ?? []));

        $matchedSkills = array_filter($jobSkills, fn($skill) => str_contains($resumeText, strtolower($skill)));
        $matchedKeywords = array_filter(
            array_slice($jobKeywords, 0, 10),
            fn($kw) => str_contains($resumeText, $kw)
        );

        return [
            'matched_skills' => array_values($matchedSkills),
            'matched_keywords' => array_values($matchedKeywords),
            'total_experience_entries' => count($resume->data['job_title'] ?? []),
        ];
    }

    /**
     * Enhance top matches with AI-generated summaries
     */
    protected function enhanceWithAISummaries(Job $job, array $matches, int $topN = 10): array
    {
        try {
            foreach (array_slice($matches, 0, $topN) as $index => $match) {
                $resume = UserResume::find($match['user_resume_id']);
                if (!$resume) continue;

                $prompt = "Job: {$job->title} at {$job->company}\n\n" .
                         "Candidate Resume Summary: " . substr(json_encode($resume->data), 0, 1000) . "\n\n" .
                         "In 2-3 sentences, explain why this candidate is a good match for this job.";

                $summary = $this->openAIService->generateContent($prompt, 100);
                $matches[$index]['ai_summary'] = $summary;
            }
        } catch (\Exception $e) {
            Log::warning('AI summary generation failed: ' . $e->getMessage());
        }

        return $matches;
    }
}
