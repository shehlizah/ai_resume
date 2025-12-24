<?php

namespace App\Services;

use App\Models\Job;
use App\Models\PostedJob;
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
     * AI-powered candidate matching for a job
     * Uses OpenAI to understand job requirements and resume content
     * Accepts both Job (scraped) and PostedJob (employer-posted) models
     */
    public function matchCandidatesForJob($job, int $limit = 50)
    {
        \Log::info("=== Starting AI Matching for Job {$job->id} ===", [
            'job_title' => $job->title,
            'job_tags' => $job->tags
        ]);

        // Get candidates with completed resumes
        $candidates = User::where('role', 'user')
            ->where('is_active', true)
            ->whereHas('resumes', function ($q) {
                $q->where('status', 'completed');
            })
            ->with(['resumes' => function ($q) {
                $q->where('status', 'completed')->latest();
            }])
            ->limit(200)
            ->get();

        \Log::info("Found {$candidates->count()} candidates with completed resumes");

        $matches = [];
        $skippedCount = 0;

        foreach ($candidates as $candidate) {
            $resume = $candidate->resumes->first();
            if (!$resume) {
                \Log::info("Skipped candidate {$candidate->id}: No resume");
                $skippedCount++;
                continue;
            }

            // Normalize resume data and skip if meaningfully empty
            $normalized = $this->normalizeResumeData($resume->data);
            if ($this->isMeaningfullyEmpty($normalized)) {
                \Log::info("Skipped candidate {$candidate->id} ({$normalized['name']}): Resume is meaningfully empty (no skills/experience/summary)");
                $skippedCount++;
                continue;
            }

            // Use AI to score this candidate against the job
            try {
                $matchResult = $this->aiScoreCandidate($job, $candidate, $resume);

                \Log::info("AI score for candidate {$candidate->id} ({$normalized['name']}): {$matchResult['score']}", [
                    'matched_skills' => $matchResult['details']['matched_skills'] ?? [],
                    'summary' => substr($matchResult['summary'], 0, 100)
                ]);

                if ($matchResult['score'] >= 70) {
                    \Log::info("✓ Candidate {$candidate->id} MATCHED with score {$matchResult['score']}");
                    $matches[] = [
                        'job_id' => $job->id,
                        'user_id' => $candidate->id,
                        'user_resume_id' => $resume->id,
                        'match_score' => $matchResult['score'],
                        'match_details' => json_encode($matchResult['details']),
                        'ai_summary' => $matchResult['summary'],
                        'status' => $matchResult['score'] >= 75 ? 'shortlisted' : 'pending',
                        'matched_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    \Log::info("✗ Candidate {$candidate->id} REJECTED: Score {$matchResult['score']} below 70% threshold");
                }
            } catch (\Exception $e) {
                \Log::warning("AI matching failed for candidate {$candidate->id}: {$e->getMessage()}");
                continue;
            }
        }

        \Log::info("=== Matching Summary ===", [
            'total_candidates_checked' => $candidates->count(),
            'skipped' => $skippedCount,
            'processed' => $candidates->count() - $skippedCount,
            'matches_found' => count($matches),
            'top_scores' => array_slice(array_column($matches, 'match_score'), 0, 5)
        ]);

        // Sort by score and take top matches
        usort($matches, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        $matches = array_slice($matches, 0, $limit);

        // Bulk insert
        if (!empty($matches)) {
            JobCandidateMatch::insert($matches);
            \Log::info("Inserted " . count($matches) . " job candidate matches");
        } else {
            \Log::warning("No matches inserted for job {$job->id}");
        }

        return count($matches);
    }

    /**
     * Use AI to score candidate against job
     * This does REAL semantic understanding, not keyword matching
     */
    protected function aiScoreCandidate($job, User $candidate, UserResume $resume): array
    {
        if (!$this->openAIService) {
            // Fallback to basic matching if no AI service
            return $this->basicScoreCandidate($job, $candidate, $resume);
        }

        // Build job context
        $jobPrompt = "Job Position: {$job->title}\n";
        $jobPrompt .= "Company: {$job->company}\n";
        $jobPrompt .= "Location: {$job->location}\n";
        $jobPrompt .= "Description: " . substr($job->description, 0, 2000) . "\n";
        $jobPrompt .= "Tags: " . (is_array($job->tags) ? implode(', ', $job->tags) : $job->tags) . "\n";

        // Build resume context - normalized data
        $resumeData = $this->normalizeResumeData($resume->data);

        \Log::info("AI Matching - Resume data for candidate {$candidate->id}", [
            'name' => $resumeData['name'] ?? 'N/A',
            'title' => $resumeData['title'] ?? 'N/A',
            'skills_count' => count($resumeData['skills'] ?? []),
            'skills' => $resumeData['skills'] ?? [],
            'job_titles' => $resumeData['job_title'] ?? []
        ]);

        $resumeText = "Candidate Title: " . ($resumeData['title'] ?? 'Not specified') . "\n";
        $resumeText .= "Summary: " . substr($resumeData['summary'] ?? '', 0, 500) . "\n";

        if (isset($resumeData['skills']) && is_array($resumeData['skills'])) {
            $resumeText .= "Skills: " . implode(', ', array_slice($resumeData['skills'], 0, 20)) . "\n";
        }

        if (isset($resumeData['job_title']) && is_array($resumeData['job_title'])) {
            $resumeText .= "Experience: " . implode('; ', array_slice($resumeData['job_title'], 0, 5)) . "\n";
        }

        // Pre-compute for heredoc
        $candidateTitle = $resumeData['title'] ?? 'Not specified';

        // AI scoring prompt with title and keyword matching emphasis
        $scorePrompt = <<<PROMPT
You are an expert HR recruiter. Score how well this candidate matches the job on a scale of 0-100.

{$jobPrompt}

CANDIDATE RESUME:
{$resumeText}

Provide your response in JSON format with these fields:
{
  "score": <0-100>,
  "matched_skills": ["skill1", "skill2", ...],
  "missing_skills": ["skill1", "skill2", ...],
  "experience_match": "junior/mid/senior/executive",
  "location_match": true/false,
  "summary": "2-3 sentence explanation of why they match (or don't match)"
}

CRITICAL SCORING CRITERIA:
1. Job Title Match: The candidate's title is "{$candidateTitle}" and the job is for "{$job->title}".
   - If titles align (e.g., both PHP Developer): score 70-100
   - If partially aligned (e.g., Developer vs Engineer): score 50-70
   - If unrelated (e.g., Manager for Developer role): score 0-40

2. Keyword Coverage: Must have at least 50% of key job keywords/technologies
   - Each missing key technology reduces score by 10-15 points

3. Experience Level:
   - Senior roles: Must have 5+ years relevant experience
   - Mid-level: Must have 2-5 years relevant experience
   - Junior: Less than 2 years acceptable

4. Required Skills: Must possess the core required skills mentioned in job description

Focus on semantic understanding and exact role fit. Penalty if title doesn't match the role type.
PROMPT;

        try {
            \Log::info("AI Matching - Candidate {$candidate->id}, Job {$job->id}: Calling OpenAI...");
            $response = $this->openAIService->generateContent($scorePrompt, 500);
            \Log::info("AI Matching - OpenAI Response: " . substr($response, 0, 200));

            // Parse JSON response
            $result = json_decode($response, true);

            if (!$result || !isset($result['score'])) {
                \Log::warning("Invalid AI response for candidate matching: $response. Falling back to basic scoring.");
                return $this->basicScoreCandidate($job, $candidate, $resume);
            }

            \Log::info("AI Matching - Score for candidate {$candidate->id}: {$result['score']}");
            return [
                'score' => max(0, min(100, (int)$result['score'])),
                'details' => [
                    'matched_skills' => $result['matched_skills'] ?? [],
                    'missing_skills' => $result['missing_skills'] ?? [],
                    'experience_match' => $result['experience_match'] ?? 'unknown',
                    'location_match' => $result['location_match'] ?? false,
                    'total_experience_entries' => count($resumeData['job_title'] ?? []),
                ],
                'summary' => $result['summary'] ?? 'No summary available',
            ];
        } catch (\Exception $e) {
            \Log::error("AI scoring error for candidate {$candidate->id}: {$e->getMessage()}. Using basic scoring.");
            return $this->basicScoreCandidate($job, $candidate, $resume);
        }
    }

    /**
     * Fallback: basic keyword-based scoring (when AI unavailable)
     */
    protected function basicScoreCandidate($job, User $candidate, UserResume $resume): array
    {
        $jobKeywords = $this->extractJobKeywords($job);
        $jobSkills = $this->extractJobSkills($job);

        // Use normalized resume data
        $resumeData = $this->normalizeResumeData($resume->data);

        $score = 0;
        $resumeText = strtolower(json_encode($resumeData));

        // Skills match (40% weight)
        $skillsMatch = 0;
        $matchedSkills = [];
        if (!empty($jobSkills)) {
            foreach ($jobSkills as $skill) {
                if (str_contains($resumeText, strtolower($skill))) {
                    $skillsMatch++;
                    $matchedSkills[] = $skill;
                }
            }
            $skillsScore = (count($jobSkills) > 0) ? ($skillsMatch / count($jobSkills)) * 40 : 0;
            $score += $skillsScore;
        }

        // Keywords match (30% weight) - MUST be at least 50% of required keywords
        $keywordMatch = 0;
        $matchedKeywords = [];
        $sampleKeywords = array_slice($jobKeywords, 0, 20);
        if (!empty($sampleKeywords)) {
            foreach ($sampleKeywords as $keyword) {
                if (str_contains($resumeText, $keyword)) {
                    $keywordMatch++;
                    $matchedKeywords[] = $keyword;
                }
            }
            $keywordPercentage = (count($sampleKeywords) > 0) ? ($keywordMatch / count($sampleKeywords)) : 0;
            // Must have at least 50% keyword match to be viable
            if ($keywordPercentage < 0.5) {
                \Log::info("Candidate {$candidate->id} rejected: Only {$keywordPercentage}% keyword match (minimum 50% required)");
                return [
                    'score' => 0,
                    'details' => ['matched_keywords' => count($matchedKeywords), 'required_keywords' => count($sampleKeywords), 'keyword_percentage' => $keywordPercentage],
                    'summary' => "Insufficient keyword match: {$keywordPercentage}% vs 50% minimum",
                ];
            }
            $keywordScore = $keywordPercentage * 30;
            $score += $keywordScore;
        }

        // Experience level (20% weight)
        $experienceScore = $this->scoreExperience($job, $resumeData);
        $score += $experienceScore * 20;

        // Location match (10% weight)
        $locationScore = 0;
        $jobLocation = strtolower($job->location ?? '');
        if (str_contains($jobLocation, 'remote') || str_contains($resumeText, strtolower($job->location))) {
            $locationScore = 1;
        }
        $score += $locationScore * 10;

        // Apply title match adjustment (10% influence)
        $titleMatch = $this->scoreTitleMatch($job->title, $resumeData['title'] ?? '');
        $finalScore = ($score * 0.9) + ($titleMatch * 10 / 100 * 10);
        $finalScore = min(100, max(0, $finalScore));

        return [
            'score' => (int) $finalScore,
            'details' => [
                'matched_skills' => array_values($matchedSkills),
                'missing_skills' => array_values(array_diff($jobSkills, $matchedSkills)),
                'title_match_score' => $titleMatch,
                'total_experience_entries' => count($resumeData['job_title'] ?? []),
            ],
            'summary' => "Matched " . count($matchedSkills) . " skills and " . count($matchedKeywords) . " keywords (Title match: {$titleMatch}%)",
        ];
    }

    /**
     * Extract keywords from job description and title
     */
    protected function extractJobKeywords($job): array
    {
        $text = strtolower($job->title . ' ' . $job->description);

        $stopWords = ['the', 'and', 'for', 'with', 'you', 'are', 'this', 'that', 'from', 'will', 'can', 'our', 'your', 'have', 'has'];

        $words = preg_split('/\W+/', $text);
        $words = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopWords));

        return array_values(array_unique($words));
    }

    /**
     * Extract skills from job tags and description
     */
    protected function extractJobSkills($job): array
    {
        $skills = [];

        if ($job->tags && is_array($job->tags)) {
            $skills = array_merge($skills, array_map('strtolower', $job->tags));
        }

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
     * Score experience level match
     */
    protected function scoreExperience($job, array $resumeData): float
    {
        $experiences = $resumeData['job_title'] ?? [];
        if (empty($experiences) || !is_array($experiences)) {
            return 0.3;
        }

        $yearCount = count($experiences);

        $jobTitle = strtolower($job->title);
        if (str_contains($jobTitle, 'senior') || str_contains($jobTitle, 'lead')) {
            return $yearCount >= 4 ? 1 : ($yearCount >= 2 ? 0.6 : 0.3);
        } elseif (str_contains($jobTitle, 'junior') || str_contains($jobTitle, 'entry')) {
            return $yearCount <= 2 ? 1 : 0.7;
        }

        return $yearCount >= 2 ? 1 : 0.6;
    }

    /**
     * Normalize resume data coming from DB. Handles strings, nulls, arrays.
     */
    protected function normalizeResumeData($raw): array
    {
        $data = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
        if (!is_array($data)) {
            $data = [];
        }

        // Ensure string fields are strings
        foreach (['name','title','email','phone','address','summary','experience','education'] as $k) {
            $v = $data[$k] ?? '';
            $data[$k] = is_string($v) ? $v : '';
        }

        // Normalize skills
        $skills = $data['skills'] ?? [];
        if (is_string($skills)) {
            // Check if it's HTML (contains tags)
            if (preg_match('/<[^>]+>/', $skills)) {
                // Extract text from HTML, then split by common separators
                $skills = strip_tags($skills);
                $skills = preg_split('/[\n\r,;]+/', $skills);
                $skills = array_values(array_filter(array_map(fn($s) => trim($s), $skills)));
            } else {
                // Plain comma-separated string
                $skills = array_values(array_filter(array_map(fn($s) => trim($s), explode(',', $skills))));
            }
        } elseif (!is_array($skills)) {
            $skills = [];
        } else {
            $skills = array_values(array_filter($skills, fn($s) => is_string($s) && trim($s) !== ''));
        }
        $data['skills'] = $skills;

        // Normalize array fields (remove nulls/empties)
        $arrayKeys = ['job_title','company','start_date','end_date','responsibilities','degree','field_of_study','university','graduation_year','education_details'];
        foreach ($arrayKeys as $k) {
            $v = $data[$k] ?? [];
            if (is_string($v) && trim($v) !== '') {
                $data[$k] = [$v];
            } elseif (!is_array($v)) {
                $data[$k] = [];
            } else {
                $data[$k] = array_values(array_filter($v, fn($x) => !(is_null($x) || (is_string($x) && trim($x) === ''))));
            }
        }

        return $data;
    }

    /**
     * Determine if resume data is meaningfully empty (no skills, no experience, no summary).
     */
    protected function isMeaningfullyEmpty(array $data): bool
    {
        $hasSkills = isset($data['skills']) && is_array($data['skills']) && count($data['skills']) > 0;
        $hasExpEntries = isset($data['job_title']) && is_array($data['job_title']) && count($data['job_title']) > 0;
        $hasSummary = isset($data['summary']) && is_string($data['summary']) && trim($data['summary']) !== '';
        $hasExperienceText = isset($data['experience']) && is_string($data['experience']) && trim($data['experience']) !== '';
        $hasEducationText = isset($data['education']) && is_string($data['education']) && trim($data['education']) !== '';

        return !($hasSkills || $hasExpEntries || $hasSummary || $hasExperienceText || $hasEducationText);
    }

    /**
     * Score how well the candidate's title matches the job title
     * Returns percentage (0-100)
     */
    protected function scoreTitleMatch(string $jobTitle, string $candidateTitle): int
    {
        if (empty($jobTitle) || empty($candidateTitle)) {
            return 0;
        }

        $jobTitle = strtolower(trim($jobTitle));
        $candidateTitle = strtolower(trim($candidateTitle));

        // Extract key role terms
        $jobTerms = preg_split('/\s+/', $jobTitle);
        $candidateTerms = preg_split('/\s+/', $candidateTitle);

        // Check for exact match
        if ($jobTitle === $candidateTitle) {
            return 100;
        }

        // Check for major keywords match (e.g., "PHP Developer" vs "PHP Web Developer")
        $matchedTerms = 0;
        $commonTerms = array_intersect($jobTerms, $candidateTerms);
        $matchedTerms = count($commonTerms);

        if ($matchedTerms >= 2) {
            return 90; // Strong match (multiple aligned keywords)
        } elseif ($matchedTerms === 1) {
            return 60; // Partial match (one major keyword)
        }

        // Check for role type alignment (e.g., Developer, Engineer, Designer, etc.)
        $roleKeywords = ['developer', 'engineer', 'designer', 'architect', 'manager', 'lead', 'senior', 'junior'];
        $jobRoles = array_intersect($jobTerms, $roleKeywords);
        $candidateRoles = array_intersect($candidateTerms, $roleKeywords);

        $roleMatch = count(array_intersect($jobRoles, $candidateRoles));
        if ($roleMatch > 0) {
            return 50; // Some role alignment
        }

        return 20; // Title mismatch
    }
}
