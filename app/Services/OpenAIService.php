<?php

namespace App\Services;

use OpenAI\Client;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Generate personalized job search recommendations
     */
    public function generateJobRecommendations(string $jobTitle, string $location = null, array $skills = [])
    {
        $cacheKey = 'job_recommendations_' . md5($jobTitle . $location . implode(',', $skills));

        return Cache::remember($cacheKey, 3600, function () use ($jobTitle, $location, $skills) {
            $prompt = $this->getJobRecommendationPrompt($jobTitle, $location, $skills);

            try {
                $response = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert career advisor and job search specialist. Provide detailed, actionable job search recommendations.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);

                $content = $response->choices[0]->message->content;
                return $this->parseJobRecommendations($content);

            } catch (\Exception $e) {
                \Log::error('OpenAI Job Recommendations Error: ' . $e->getMessage());
                return $this->getFallbackJobRecommendations($jobTitle);
            }
        });
    }

    /**
     * Generate interview preparation content - FIXED PARSING
     */
    public function generateInterviewPrep(string $jobTitle, string $experienceLevel = 'mid', string $companyType = 'general')
    {
        $cacheKey = 'interview_prep_' . md5($jobTitle . $experienceLevel . $companyType);

        return Cache::remember($cacheKey, 3600, function () use ($jobTitle, $experienceLevel, $companyType) {
            $prompt = $this->getInterviewPrepPrompt($jobTitle, $experienceLevel, $companyType);

            try {
                $response = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert interview coach with 15+ years of experience. Provide comprehensive, practical interview preparation advice in a clear, easy-to-read format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 4000, // Increased token limit
                ]);

                $content = $response->choices[0]->message->content;
                return $this->parseInterviewPrep($content);

            } catch (\Exception $e) {
                \Log::error('OpenAI Interview Prep Error: ' . $e->getMessage());
                return $this->getFallbackInterviewPrep($jobTitle);
            }
        });
    }

    /**
     * Job Recommendation Prompt
     */
    private function getJobRecommendationPrompt(string $jobTitle, $location, array $skills): string
    {
        $skillsText = !empty($skills) ? "Key Skills: " . implode(', ', $skills) . "\n" : '';
        $locationText = $location ? "Preferred Location: " . $location . "\n" : '';

        return <<<PROMPT
I need comprehensive job search recommendations for the following position:

Job Title: {$jobTitle}
{$locationText}{$skillsText}

Please provide a detailed job search strategy in the following JSON format:

{
    "job_boards": [
        {
            "name": "Job Board Name",
            "url": "https://example.com",
            "category": "General/Tech/Remote/Industry-Specific",
            "search_query": "specific search terms to use",
            "priority": "high/medium/low",
            "why_recommended": "brief explanation"
        }
    ],
    "search_strategies": [
        {
            "strategy": "Strategy name",
            "description": "How to execute this strategy",
            "tips": ["tip 1", "tip 2"]
        }
    ],
    "keywords": ["keyword1", "keyword2", "keyword3"],
    "alternative_titles": ["title1", "title2"],
    "networking_tips": ["tip1", "tip2", "tip3"],
    "industry_insights": "Brief overview of current market for this role"
}

Recommend at least 15-20 relevant job boards.
PROMPT;
    }

    /**
     * SIMPLIFIED Interview Preparation Prompt
     */
    private function getInterviewPrepPrompt(string $jobTitle, string $experienceLevel, string $companyType): string
    {
        return <<<PROMPT
Create comprehensive interview preparation for a {$experienceLevel}-level {$jobTitle} position at a {$companyType} company.

Format your response EXACTLY as follows:

## COMMON INTERVIEW QUESTIONS

**Question 1: [First question]**
Category: [Behavioral/Technical/Situational]
Sample Answer: [Detailed answer using STAR method if behavioral]
Key Points:
- Point 1
- Point 2

---

**Question 2: [Second question]**
Category: [Behavioral/Technical/Situational]
Sample Answer: [Detailed answer]
Key Points:
- Point 1
- Point 2

---

[Continue for 20-25 questions total]

## TECHNICAL TOPICS TO STUDY

- **SQL Queries**
  Why it's Important: [explanation]
  Key Concepts: [concepts]

- **Data Visualization**
  Why it's Important: [explanation]
  Key Concepts: [concepts]

[Continue for all relevant topics]

## QUESTIONS TO ASK THE INTERVIEWER

1. What does success look like in this role?
2. What are the team's biggest challenges?
3. How is performance measured?
[Continue for 10-12 questions]

## SALARY NEGOTIATION TIPS

- Market range: [range]
- When to discuss: [timing]
- Negotiation strategies: [strategies]

## DAY-OF-INTERVIEW CHECKLIST

- Arrive 10-15 minutes early
- Bring: Resume copies, notepad, pen
- Dress: [appropriate attire]
- Turn off phone
- Final tips: [tips]

Provide specific, actionable advice for {$jobTitle} at {$experienceLevel} level.
PROMPT;
    }

    /**
     * Parse AI response for job recommendations (JSON)
     */
    private function parseJobRecommendations(string $content): array
    {
        // Try to extract JSON from response
        if (preg_match('/\{[\s\S]*\}/m', $content, $matches)) {
            try {
                $data = json_decode($matches[0], true);
                if ($data) return $data;
            } catch (\Exception $e) {
                \Log::error('JSON Parse Error: ' . $e->getMessage());
            }
        }

        return [
            'raw_content' => $content,
            'job_boards' => [],
            'search_strategies' => [],
        ];
    }

    /**
     * FIXED: Parse AI response for interview prep
     */
    private function parseInterviewPrep(string $content): array
    {
        $sections = [
            'common_questions' => [],
            'technical_topics' => '',
            'questions_to_ask' => [],
            'salary_tips' => '',
            'day_of_tips' => '',
            'raw_content' => $content
        ];

        try {
            // Parse questions - UPDATED REGEX to handle numbered questions
            preg_match_all(
                '/\*\*Question\s+\d+:\s*(.+?)\*\*\s*\nCategory:\s*(.+?)\s*\nSample Answer:\s*(.+?)(?:\nKey Points:.*?)?(?=\n---|\n\*\*Question|\n##|\z)/s',
                $content,
                $questions
            );

            if (!empty($questions[1])) {
                for ($i = 0; $i < count($questions[1]); $i++) {
                    $sections['common_questions'][] = [
                        'question' => trim($questions[1][$i]),
                        'category' => trim($questions[2][$i]),
                        'sample_answer' => trim(strip_tags($questions[3][$i]))
                    ];
                }
            }

            // Extract questions to ask - IMPROVED
            if (preg_match('/## QUESTIONS TO ASK THE INTERVIEWER.*?\n\n(.*?)(?=\n##|\z)/s', $content, $askMatches)) {
                preg_match_all('/^\d+\.\s*(.+?)$/m', $askMatches[1], $askQuestions);
                if (!empty($askQuestions[1])) {
                    $sections['questions_to_ask'] = array_map('trim', $askQuestions[1]);
                }
            }

            // Extract technical topics
            if (preg_match('/## TECHNICAL TOPICS TO STUDY.*?\n\n(.*?)(?=\n##|\z)/s', $content, $techMatches)) {
                $sections['technical_topics'] = trim($techMatches[1]);
            }

            // Extract salary tips
            if (preg_match('/## SALARY NEGOTIATION TIPS.*?\n\n(.*?)(?=\n##|\z)/s', $content, $salaryMatches)) {
                $sections['salary_tips'] = trim($salaryMatches[1]);
            }

            // Extract day-of tips
            if (preg_match('/## DAY-OF-INTERVIEW CHECKLIST.*?\n\n(.*?)(?=\n##|\z)/s', $content, $dayMatches)) {
                $sections['day_of_tips'] = trim($dayMatches[1]);
            }

        } catch (\Exception $e) {
            \Log::error('Interview Prep Parsing Error: ' . $e->getMessage());
        }

        return $sections;
    }

    /**
     * Fallback job recommendations
     */
    private function getFallbackJobRecommendations(string $jobTitle): array
    {
        return [
            'job_boards' => [
                [
                    'name' => 'LinkedIn Jobs',
                    'url' => 'https://www.linkedin.com/jobs/search/?keywords=' . urlencode($jobTitle),
                    'category' => 'General',
                    'priority' => 'high',
                ],
                [
                    'name' => 'Indeed',
                    'url' => 'https://www.indeed.com/jobs?q=' . urlencode($jobTitle),
                    'category' => 'General',
                    'priority' => 'high',
                ],
                [
                    'name' => 'Glassdoor',
                    'url' => 'https://www.glassdoor.com/Job/jobs.htm?sc.keyword=' . urlencode($jobTitle),
                    'category' => 'General',
                    'priority' => 'high',
                ],
            ],
            'keywords' => explode(' ', $jobTitle),
        ];
    }

    /**
     * Fallback interview prep
     */
    private function getFallbackInterviewPrep(string $jobTitle): array
    {
        return [
            'common_questions' => [
                [
                    'question' => 'Tell me about yourself and your background',
                    'category' => 'Behavioral',
                    'sample_answer' => 'Start with your current role, then briefly cover your relevant experience, key achievements, and why you\'re interested in this ' . $jobTitle . ' position. Focus on what makes you a great fit for this specific role.'
                ],
                [
                    'question' => 'Why are you interested in this ' . $jobTitle . ' position?',
                    'category' => 'Behavioral',
                    'sample_answer' => 'Express genuine enthusiasm for the role and company. Mention specific aspects of the job that align with your career goals and skills.'
                ],
                [
                    'question' => 'What are your greatest strengths?',
                    'category' => 'Behavioral',
                    'sample_answer' => 'Choose 2-3 strengths directly relevant to the ' . $jobTitle . ' role. Provide specific examples with measurable results.'
                ],
                [
                    'question' => 'What is your biggest weakness?',
                    'category' => 'Behavioral',
                    'sample_answer' => 'Choose a real but not critical weakness. Explain the steps you\'re taking to improve. Show self-awareness and commitment to growth.'
                ],
                [
                    'question' => 'Describe a challenging situation and how you handled it',
                    'category' => 'Situational',
                    'sample_answer' => 'Use STAR method: Situation, Task, Action, Result. Choose a challenge relevant to the ' . $jobTitle . ' role.'
                ],
            ],
            'questions_to_ask' => [
                'What does success look like in this role in the first 6 months?',
                'What are the biggest challenges facing the team?',
                'How does this role contribute to company goals?',
                'What opportunities for professional development are available?',
                'Can you describe the team culture?',
                'What are the next steps in the interview process?'
            ],
            'salary_tips' => 'Research market rate for ' . $jobTitle . '. Wait for employer to bring up salary first. Provide a range, not a single number. Be prepared to justify with experience and skills.',
            'day_of_tips' => 'Arrive 10-15 minutes early. Bring resume copies, notepad, pen. Dress professionally. Turn off phone. Make eye contact and offer firm handshake.',
            'technical_topics' => 'Study core concepts relevant to ' . $jobTitle . ' including technical skills, tools, and industry best practices.',
            'raw_content' => 'Basic interview preparation for ' . $jobTitle
        ];
    }

    // ========================================
    // COVER LETTER GENERATION
    // ========================================

    public function generateCoverLetter(array $data)
    {
        $prompt = $this->getCoverLetterPrompt($data);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert professional cover letter writer with 20+ years of experience.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.8,
                'max_tokens' => 2000,
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            \Log::error('OpenAI Cover Letter Error: ' . $e->getMessage());
            throw new \Exception('Failed to generate cover letter. Please try again.');
        }
    }


    /**
     * Generate AI-powered job recommendations from resume file (binary or text)
     */
    public function generateJobsFromResumeFile(string $filePath, string $location = null, int $limit = 5): array
    {
        try {
            // Read the file and encode as base64 for sending to AI
            if (!file_exists($filePath)) {
                \Log::warning('Resume file not found: ' . $filePath);
                return [];
            }

            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                \Log::warning('Failed to read resume file: ' . $filePath);
                return [];
            }

            // Determine file type
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $fileType = match ($extension) {
                'pdf' => 'application/pdf',
                'docx', 'doc' => 'application/msword',
                default => 'application/octet-stream',
            };

            // Encode file as base64
            $fileBase64 = base64_encode($fileContent);

            $prompt = $this->buildJobRecommendationFromFilePrompt($fileBase64, $fileType, $location, $limit);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert career advisor who reads resumes and generates job recommendations. Return ONLY valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2500,
            ]);

            $content = $response->choices[0]->message->content;
            return $this->parseJobMatches($content);

        } catch (\Exception $e) {
            \Log::error('OpenAI Job Generation from File Error: ' . $e->getMessage());
            return [];
        }
    }

    private function buildJobRecommendationFromFilePrompt(string $fileBase64, string $fileType, ?string $location, int $limit): string
    {
        $locationLine = $location ? "\nPreferred Location: $location" : '';

        return <<<PROMPT
You have received a resume file in base64 format. Please decode and analyze it to generate $limit relevant job recommendations.

Resume File (base64): {$fileBase64}
File Type: {$fileType}{$locationLine}

Even if the resume format is unclear or partially corrupted, do your best to extract information about the candidate's skills and experience.

Return ONLY a valid JSON array (no markdown, no extra text) with exactly this structure:
[
  {
    "title": "Job Title",
    "company": "Company Name",
    "location": "City, State or Remote",
    "salary": "$min - $max or Competitive",
    "description": "Brief job description matching this candidate's profile",
    "match_score": 85
  }
]

Focus on jobs that match the candidate's skills, experience level, and location. Match scores should be 70-95.
PROMPT;
    }

    /**
     * Generate AI-powered job recommendations from resume text
     */
    public function generateJobsFromResume(string $resumeText, string $location = null, int $limit = 5): array
    {
        try {
            $prompt = $this->buildJobRecommendationPrompt($resumeText, $location, $limit);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert career advisor. Analyze resumes and generate highly relevant job recommendations. Return ONLY valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2500,
            ]);

            $content = $response->choices[0]->message->content;
            return $this->parseJobMatches($content);

        } catch (\Exception $e) {
            \Log::error('OpenAI Job Generation Error: ' . $e->getMessage());
            return [];
        }
    }

    private function buildJobRecommendationPrompt(string $resumeText, ?string $location, int $limit): string
    {
        $locationLine = $location ? "\nPreferred Location: $location" : '';

        return <<<PROMPT
Analyze this resume and generate $limit relevant job recommendations:

RESUME:
{$resumeText}{$locationLine}

Return ONLY a valid JSON array (no markdown, no extra text) with exactly this structure:
[
  {
    "title": "Job Title",
    "company": "Company Name",
    "location": "City, State or Remote",
    "salary": "$min - $max or Competitive",
    "description": "Brief job description matching this candidate's profile",
    "match_score": 85
  }
]

Focus on jobs that match the candidate's skills, experience level, and location. Match scores should be 70-95.
PROMPT;
    }

    private function parseJobMatches(string $json): array
    {
        try {
            // Extract JSON from response (handle markdown code blocks)
            if (preg_match('/\[[\s\S]*\]/', $json, $matches)) {
                $json = $matches[0];
            }

            $jobs = json_decode($json, true);

            if (!is_array($jobs)) {
                \Log::warning('Invalid job JSON from OpenAI', ['response' => substr($json, 0, 200)]);
                return [];
            }

            // Validate and clean job data
            return array_filter(array_map(fn ($job) => [
                'id' => 'ai-' . md5($job['title'] . $job['company']),
                'title' => $job['title'] ?? 'Job',
                'company' => $job['company'] ?? 'Company',
                'location' => $job['location'] ?? 'Remote',
                'salary' => $job['salary'] ?? 'Competitive',
                'description' => $job['description'] ?? '',
                'match_score' => min(99, max(0, $job['match_score'] ?? 75))
            ], $jobs));

        } catch (\Exception $e) {
            \Log::error('Job parsing error: ' . $e->getMessage());
            return [];
        }
    }

    private function getCoverLetterPrompt(array $data): string

    {
        $userName = $data['user_name'] ?? 'The Applicant';
        $userEmail = $data['user_email'] ?? '';
        $userPhone = $data['user_phone'] ?? '';
        $userAddress = $data['user_address'] ?? '';

        $recipientName = $data['recipient_name'] ?? 'Hiring Manager';
        $companyName = $data['company_name'] ?? 'The Company';
        $companyAddress = $data['company_address'] ?? '';

        $jobDescription = $data['job_description'] ?? '';
        $additionalInfo = $data['additional_info'] ?? '';

        $jobDescriptionSection = $jobDescription ? "\n\nJob Description:\n{$jobDescription}" : '';
        $additionalInfoSection = $additionalInfo ? "\n\nSkills & Experience:\n{$additionalInfo}" : '';

        return <<<PROMPT
Write a professional cover letter:

APPLICANT: {$userName}, {$userEmail}, {$userPhone}, {$userAddress}
RECIPIENT: {$recipientName}, {$companyName}, {$companyAddress}
{$jobDescriptionSection}{$additionalInfoSection}

Use proper business letter format. Make it compelling, professional, and tailored (300-400 words).
PROMPT;
    }
}
