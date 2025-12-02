<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class JobMatchService
{
    /**
     * Catalog of roles, keywords, and base job templates.
     */
    protected array $catalog = [
        'software_engineering' => [
            'label' => 'Software Engineering',
            'default_role' => 'Software Engineer',
            'keywords' => [
                'php', 'laravel', 'symfony', 'livewire', 'javascript', 'typescript', 'react', 'vue',
                'node', 'python', 'django', 'flask', 'golang', 'aws', 'azure', 'gcp', 'docker',
                'kubernetes', 'api', 'microservices', 'sql', 'mysql', 'postgres', 'redis', 'devops'
            ],
            'jobs' => [
                [
                    'title' => 'Senior {role} (Laravel + API)',
                    'company' => 'CloudForge Labs',
                    'salary' => '$115,000 - $145,000',
                    'description' => 'Scale subscription platforms, design resilient APIs, and own deployment pipelines.',
                ],
                [
                    'title' => '{role} - Product Enablement',
                    'company' => 'SignalStack',
                    'salary' => '$105,000 - $135,000',
                    'description' => 'Partner with product managers to deliver full-stack features with measurable impact.',
                ],
                [
                    'title' => 'Lead {role} (Remote)',
                    'company' => 'Northwind Digital',
                    'salary' => '$125,000 - $155,000',
                    'description' => 'Mentor engineers, modernize services, and champion clean architecture practices.',
                ],
                [
                    'title' => '{role}, Emerging Products',
                    'company' => 'Studio 42',
                    'salary' => '$98,000 - $128,000',
                    'description' => 'Rapidly prototype features, integrate third-party APIs, and optimize performance.',
                ],
            ],
        ],
        'data_analytics' => [
            'label' => 'Data & Analytics',
            'default_role' => 'Data Analyst',
            'keywords' => [
                'sql', 'python', 'r', 'tableau', 'power bi', 'lookml', 'dbt', 'statistics', 'ml', 'machine learning',
                'pandas', 'numpy', 'data warehouse', 'etl', 'analytics', 'bigquery', 'snowflake'
            ],
            'jobs' => [
                [
                    'title' => 'Senior {role}',
                    'company' => 'InsightLoop',
                    'salary' => '$110,000 - $135,000',
                    'description' => 'Translate product questions into dashboards and curated datasets for leadership.',
                ],
                [
                    'title' => '{role} - Product Analytics',
                    'company' => 'Sparrow Health',
                    'salary' => '$95,000 - $120,000',
                    'description' => 'Own experimentation, A/B testing, and growth funnels for customer cohorts.',
                ],
                [
                    'title' => 'Lead Analytics Partner',
                    'company' => 'Luminary Finance',
                    'salary' => '$120,000 - $150,000',
                    'description' => 'Build trusted models and KPI frameworks for exec visibility.',
                ],
            ],
        ],
        'product_management' => [
            'label' => 'Product Management',
            'default_role' => 'Product Manager',
            'keywords' => [
                'product manager', 'roadmap', 'stakeholder', 'go-to-market', 'discovery', 'ux', 'ui', 'agile',
                'scrum', 'jira', 'launch', 'experimentation', 'feature', 'customer research', 'kpi'
            ],
            'jobs' => [
                [
                    'title' => 'Senior {role} (Platform)',
                    'company' => 'Atlas Metrics',
                    'salary' => '$125,000 - $160,000',
                    'description' => 'Drive platform roadmap, prioritize technical debt, and align engineering + GTM teams.',
                ],
                [
                    'title' => '{role} - Growth',
                    'company' => 'Nova Commerce',
                    'salary' => '$115,000 - $145,000',
                    'description' => 'Own acquisition funnels, experiment roadmap, and data-informed prioritization.',
                ],
                [
                    'title' => 'Group {role}',
                    'company' => 'Orbit Mobility',
                    'salary' => '$140,000 - $180,000',
                    'description' => 'Lead multi-squad outcomes, coach PMs, and evangelize product thinking.',
                ],
            ],
        ],
        'design' => [
            'label' => 'Design & Creative',
            'default_role' => 'Product Designer',
            'keywords' => [
                'figma', 'sketch', 'ux', 'ui', 'wireframe', 'prototype', 'design system', 'visual design',
                'branding', 'motion', 'illustration', 'accessibility'
            ],
            'jobs' => [
                [
                    'title' => 'Lead {role}',
                    'company' => 'Canvasly',
                    'salary' => '$110,000 - $140,000',
                    'description' => 'Shape cohesive design systems and ship delightful end-to-end journeys.',
                ],
                [
                    'title' => '{role} - Growth',
                    'company' => 'Maple Labs',
                    'salary' => '$100,000 - $125,000',
                    'description' => 'Craft experiments that lift conversion and retention metrics.',
                ],
                [
                    'title' => '{role} (Contract, Remote)',
                    'company' => 'Bright Studios',
                    'salary' => '$85/hr - $110/hr',
                    'description' => 'Partner with marketing to deliver multi-channel creative campaigns.',
                ],
            ],
        ],
        'marketing' => [
            'label' => 'Marketing & Growth',
            'default_role' => 'Growth Marketer',
            'keywords' => [
                'seo', 'sem', 'ppc', 'campaign', 'crm', 'hubspot', 'marketo', 'performance marketing',
                'content', 'copywriting', 'email', 'automation', 'demand gen', 'paid social'
            ],
            'jobs' => [
                [
                    'title' => 'Senior {role}',
                    'company' => 'Beacon Apps',
                    'salary' => '$105,000 - $130,000',
                    'description' => 'Architect omni-channel funnels and optimize CAC to LTV ratios.',
                ],
                [
                    'title' => '{role} - Lifecycle',
                    'company' => 'Kindred Health',
                    'salary' => '$95,000 - $120,000',
                    'description' => 'Design retention journeys spanning onboarding through upsell.',
                ],
                [
                    'title' => 'Head of {role}',
                    'company' => 'Volt Commerce',
                    'salary' => '$130,000 - $165,000',
                    'description' => 'Scale paid channels, lead in-house creatives, and report growth KPIs to execs.',
                ],
            ],
        ],
        'operations' => [
            'label' => 'Operations & Project Management',
            'default_role' => 'Operations Manager',
            'keywords' => [
                'operations', 'supply chain', 'project manager', 'program manager', 'pmp', 'six sigma',
                'lean', 'process', 'forecast', 'budget', 'cross-functional', 'timeline'
            ],
            'jobs' => [
                [
                    'title' => 'Senior {role}',
                    'company' => 'Harbor Logistics',
                    'salary' => '$95,000 - $125,000',
                    'description' => 'Optimize processes, lead retros, and drive measurable efficiency gains.',
                ],
                [
                    'title' => '{role} - Strategic Programs',
                    'company' => 'Evergreen Energy',
                    'salary' => '$110,000 - $140,000',
                    'description' => 'Manage portfolios, align exec sponsors, and surface risk signals early.',
                ],
                [
                    'title' => 'Program Lead ({role})',
                    'company' => 'Atlas Cloud',
                    'salary' => '$105,000 - $135,000',
                    'description' => 'Coordinate global launches, storytelling status to stakeholders.',
                ],
            ],
        ],
        'generalist' => [
            'label' => 'Generalist Professional',
            'default_role' => 'Professional',
            'keywords' => [],
            'jobs' => [
                [
                    'title' => '{role} - Client Services',
                    'company' => 'Pioneer Partners',
                    'salary' => '$85,000 - $110,000',
                    'description' => 'Manage client relationships, deliver polished presentations, and coordinate deliverables.',
                ],
                [
                    'title' => '{role} (Remote-First)',
                    'company' => 'Summit Collective',
                    'salary' => '$90,000 - $115,000',
                    'description' => 'Lead cross-functional initiatives and own reporting rhythms.',
                ],
                [
                    'title' => 'Strategic {role}',
                    'company' => 'Blue Ridge Consulting',
                    'salary' => '$100,000 - $130,000',
                    'description' => 'Synthesize research, prepare exec-ready insights, and mentor junior talent.',
                ],
            ],
        ],
    ];

    /**
     * Build a profile from structured resume data stored in DB.
     */
    public function analyzeStructuredResume(null|array|string $raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (empty($raw) || !is_array($raw)) {
            return [];
        }

        $skills = $this->normalizeSkills($raw['skills'] ?? '');
        $summary = strip_tags((string) ($raw['summary'] ?? ''));
        $experience = strip_tags((string) ($raw['experience'] ?? ''));
        $jobTitles = $raw['job_title'] ?? [];

        $textCorpus = trim($summary . ' ' . $experience . ' ' . implode(' ', (array) $jobTitles));

        return [
            'preferred_title' => $raw['title'] ?? Arr::first((array) $jobTitles),
            'skills' => $skills,
            'raw_text' => $textCorpus,
            'experience_years' => $this->guessExperienceYears($textCorpus),
        ];
    }

    /**
     * Build a profile based on the uploaded temporary resume file.
     */
    public function analyzeUploadedResume(?string $relativePath): array
    {
        if (empty($relativePath)) {
            return [];
        }

        $fullPath = storage_path('app/' . ltrim($relativePath, '/'));

        if (!file_exists($fullPath)) {
            \Log::warning('Resume file not found at: ' . $fullPath);
            return [];
        }

        $text = $this->extractTextFromFile($fullPath);

        // Log what we extracted for debugging
        \Log::info('Extracted text from resume', [
            'file' => $relativePath,
            'text_length' => strlen($text),
            'text_preview' => substr($text, 0, 300)
        ]);

        // Accept any extracted text - don't be strict
        // Even empty text will trigger fallback to generic jobs
        $skills = $this->guessSkillsFromText($text);

        return [
            'preferred_title' => $this->guessTitle($text) ?? 'Professional',
            'skills' => $skills,
            'raw_text' => $text ?? '',
            'experience_years' => $this->guessExperienceYears($text),
        ];
    }

    /**
     * Generate job matches tailored to the profile and options.
     */
    public function generateMatches(array $profile, array $options = []): array
    {
        $industryKey = $this->detectIndustry($profile);
        $catalog = $this->catalog[$industryKey];
        $limit = max(1, (int) ($options['limit'] ?? 5));
        $location = $options['location'] ?? 'Remote';
        $explicitRole = $options['job_title'] ?? null;

        $templates = $catalog['jobs'];
        shuffle($templates);
        $matches = [];

        foreach ($templates as $idx => $template) {
            $role = $explicitRole ?: ($profile['preferred_title'] ?? $catalog['default_role']);
            $title = str_replace('{role}', $role, $template['title']);
            $description = $this->personalizeDescription($template['description'], $profile);
            $matchScore = $this->calculateMatchScore($profile, $catalog['keywords']);

            $matches[] = [
                'id' => $industryKey . '-' . ($idx + 1) . '-' . substr((string) Str::uuid(), 0, 8),
                'title' => $title,
                'company' => $template['company'],
                'location' => $location,
                'salary' => $template['salary'],
                'description' => $description,
                'match_score' => $matchScore,
                'apply_url' => $template['apply_url'] ?? '#'
            ];

            if (count($matches) >= $limit) {
                break;
            }
        }

        return $matches;
    }

    /**
     * Determine the best-fit industry for the resume profile.
     */
    protected function detectIndustry(array $profile): string
    {
        $skills = array_map(fn ($skill) => strtolower($skill), $profile['skills'] ?? []);
        $text = strtolower($profile['raw_text'] ?? '');

        $bestMatch = 'generalist';
        $bestScore = 0;

        foreach ($this->catalog as $key => $entry) {
            if ($key === 'generalist') {
                continue;
            }

            $score = 0;

            foreach ($entry['keywords'] as $keyword) {
                if (in_array($keyword, $skills, true)) {
                    $score += 3;
                } elseif (str_contains($text, $keyword)) {
                    $score += 1;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $key;
            }
        }

        return $bestMatch;
    }

    protected function normalizeSkills(null|string|array $value): array
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        if (!$value) {
            return [];
        }

        $value = str_replace(['\n', '\r'], ',', $value);
        $parts = array_filter(array_map(fn ($item) => trim($item), explode(',', $value)));

        return array_values(array_unique($parts));
    }

    protected function summarizeSkills(array $skills): string
    {
        if (empty($skills)) {
            return '';
        }

        return implode(', ', array_slice($skills, 0, 4));
    }

    protected function calculateMatchScore(array $profile, array $keywords): int
    {
        $skills = array_map('strtolower', $profile['skills'] ?? []);
        $matches = 0;

        foreach ($skills as $skill) {
            if (in_array($skill, $keywords, true)) {
                $matches++;
            }
        }

        $base = 72 + min($matches * 4, 18);
        $variance = random_int(0, 6);

        return min(99, $base + $variance);
    }

    protected function personalizeDescription(string $base, array $profile): string
    {
        $skillsSnippet = $this->summarizeSkills($profile['skills'] ?? []);
        $experience = $profile['experience_years'] ?? null;
        $extra = [];

        if ($skillsSnippet) {
            $extra[] = 'Stack focus: ' . $skillsSnippet;
        }

        if ($experience) {
            $extra[] = $experience . '+ years experience welcomed.';
        }

        if ($extra) {
            $base .= ' ' . implode(' ', $extra);
        }

        return $base;
    }

    protected function guessTitle(string $text): ?string
    {
        $patterns = [
            'product manager', 'software engineer', 'full stack developer', 'frontend developer',
            'backend developer', 'data analyst', 'data scientist', 'product designer', 'marketing manager',
            'operations manager', 'project manager', 'account manager', 'customer success manager',
        ];

        $lower = strtolower($text);
        foreach ($patterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return Str::title($pattern);
            }
        }

        return null;
    }

    protected function guessSkillsFromText(string $text): array
    {
        $text = strtolower($text);
        $skills = [];

        foreach ($this->catalog as $entry) {
            foreach ($entry['keywords'] as $keyword) {
                if (str_contains($text, $keyword)) {
                    $skills[] = Str::upper($keyword) === $keyword ? $keyword : Str::title($keyword);
                }
            }
        }

        return array_values(array_unique($skills));
    }

    protected function guessExperienceYears(string $text): ?int
    {
        if (preg_match('/(\d{1,2})\s*\+?\s*years?/i', $text, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function extractTextFromFile(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'docx' => $this->extractFromDocx($path),
            'doc' => $this->extractFromBinary($path),
            'pdf' => $this->extractFromPdf($path),
            default => $this->extractFromBinary($path),
        };
    }

    protected function extractFromDocx(string $path): string
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                \Log::warning('Failed to open DOCX file as ZIP: ' . $path);
                return '';
            }

            // First try to get document.xml
            $index = $zip->locateName('word/document.xml');
            if ($index !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();

                // Extract text from XML, stripping all tags
                $text = strip_tags($data);
                return $this->cleanText($text);
            }

            $zip->close();
            return '';
        } catch (\Exception $e) {
            \Log::error('DOCX extraction error: ' . $e->getMessage());
            return '';
        }
    }

    protected function extractFromPdf(string $path): string
    {
        try {
            // Try using Smalot PDF Parser if available
            if (class_exists('\Smalot\PdfParser\Parser')) {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($path);
                    $text = $pdf->getText();
                    
                    if (!empty($text)) {
                        \Log::info('PDF extracted using PdfParser library', [
                            'text_length' => strlen($text)
                        ]);
                        return $this->cleanText($text);
                    }
                } catch (\Exception $e) {
                    \Log::warning('PdfParser failed, falling back to regex', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Fallback to regex-based extraction
            $content = @file_get_contents($path);
            if ($content === false) {
                \Log::warning('Failed to read PDF file: ' . $path);
                return '';
            }

            // Try to extract readable text from PDF binary
            // Look for text streams in PDF
            $text = '';
            if (preg_match_all('/BT\s+(.+?)\s+ET/s', $content, $matches)) {
                $text = implode(' ', $matches[1]);
                // Remove PDF escape sequences
                $text = preg_replace('/\\\\[0-3][0-7]{0,2}/', ' ', $text);
                // Extract text from parentheses (PDF text strings)
                $text = preg_replace('/\(([^)]+)\)/', '$1', $text);
                // Remove Tj and other PDF operators
                $text = preg_replace('/\s*Tj\s*/', ' ', $text);
                $text = preg_replace('/\s*TJ\s*/', ' ', $text);
                $cleaned = $this->cleanText($text);
                if (strlen($cleaned) > 100) {
                    \Log::info('PDF extracted using regex method', [
                        'text_length' => strlen($cleaned)
                    ]);
                    return $cleaned;
                }
            }

            // Last resort: extract any printable ASCII text (very crude)
            preg_match_all('/[a-zA-Z0-9\s\.,;:\-]{4,}/', $content, $matches);
            if (!empty($matches[0])) {
                $text = implode(' ', $matches[0]);
                $cleaned = $this->cleanText($text);
                \Log::info('PDF extracted using ASCII fallback', [
                    'text_length' => strlen($cleaned)
                ]);
                return $cleaned;
            }

            \Log::warning('PDF extraction returned no usable text', ['path' => $path]);
            return '';
        } catch (\Exception $e) {
            \Log::error('PDF extraction error: ' . $e->getMessage());
            return '';
        }
    }

    protected function extractFromBinary(string $path): string
    {
        try {
            $content = @file_get_contents($path);
            if ($content === false) {
                return '';
            }

            // Extract readable ASCII text from binary files
            preg_match_all('/[\x20-\x7E]+/', $content, $matches);
            if (!empty($matches[0])) {
                // Filter out short fragments (< 3 chars)
                $filtered = array_filter($matches[0], fn ($str) => strlen($str) > 2);
                $text = implode(' ', $filtered);
                return $this->cleanText($text);
            }

            return '';
        } catch (\Exception $e) {
            \Log::error('Binary file extraction error: ' . $e->getMessage());
            return '';
        }
    }

    protected function cleanText(string $text): string
    {
        // Decode common PDF encodings
        $text = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $text);

        // Remove excessive whitespace but preserve line breaks for structure
        $text = preg_replace('/\n\s*\n/', "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/ +/', ' ', $text);

        return trim($text);
    }
}
