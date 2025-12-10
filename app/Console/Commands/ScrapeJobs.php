<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Job;
use Carbon\Carbon;

class ScrapeJobs extends Command
{
    protected $signature = 'jobs:scrape {--limit=7 : Number of jobs to scrape}';
    protected $description = 'Scrape jobs from public job boards';

    public function handle()
    {
        $limit = $this->option('limit');
        $this->info("Scraping up to {$limit} jobs...");

        $jobs = [];

        // Scrape from GitHub Jobs (public API)
        $jobs = array_merge($jobs, $this->scrapeGitHubJobs());

        // Scrape from RemoteOK (public API)
        $jobs = array_merge($jobs, $this->scrapeRemoteOK());

        // Scrape from Arbeitnow (public API)
        $jobs = array_merge($jobs, $this->scrapeArbeitnow());

        // Limit to requested number
        $jobs = array_slice($jobs, 0, $limit);

        // Save to database
        foreach ($jobs as $jobData) {
            Job::updateOrCreate(
                ['external_id' => $jobData['external_id']],
                $jobData
            );
        }

        $this->info("Successfully scraped and saved " . count($jobs) . " jobs!");

        return Command::SUCCESS;
    }

    private function scrapeGitHubJobs()
    {
        $jobs = [];

        try {
            // Using GitHub's public job search
            $response = Http::timeout(10)->get('https://remotive.com/api/remote-jobs', [
                'category' => 'software-dev',
                'limit' => 3
            ]);

            if ($response->successful()) {
                $data = $response->json();

                foreach ($data['jobs'] ?? [] as $job) {
                    $jobs[] = [
                        'external_id' => 'remotive_' . $job['id'],
                        'title' => $job['title'],
                        'company' => $job['company_name'],
                        'location' => $job['candidate_required_location'] ?? 'Remote',
                        'type' => 'Full Time',
                        'description' => strip_tags($job['description'] ?? ''),
                        'salary' => $job['salary'] ?? null,
                        'tags' => json_encode(['Remote', 'Software Development']),
                        'posted_at' => Carbon::parse($job['publication_date']),
                        'source' => 'Remotive',
                        'url' => $job['url']
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->warn("Failed to scrape from Remotive: " . $e->getMessage());
        }

        return $jobs;
    }

    private function scrapeRemoteOK()
    {
        $jobs = [];

        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'JobSease/1.0'])
                ->get('https://remoteok.com/api');

            if ($response->successful()) {
                $data = $response->json();

                // Skip first item (metadata)
                $jobList = array_slice($data, 1, 2);

                foreach ($jobList as $job) {
                    if (!isset($job['id'])) continue;

                    $jobs[] = [
                        'external_id' => 'remoteok_' . $job['id'],
                        'title' => $job['position'] ?? 'Unknown Position',
                        'company' => $job['company'] ?? 'Unknown Company',
                        'location' => $job['location'] ?? 'Remote',
                        'type' => 'Full Time',
                        'description' => strip_tags($job['description'] ?? ''),
                        'salary' => $job['salary_min'] ?? null,
                        'tags' => json_encode(array_slice($job['tags'] ?? [], 0, 3)),
                        'posted_at' => isset($job['date']) ? Carbon::createFromTimestamp($job['date']) : now(),
                        'source' => 'RemoteOK',
                        'url' => $job['url'] ?? 'https://remoteok.com'
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->warn("Failed to scrape from RemoteOK: " . $e->getMessage());
        }

        return $jobs;
    }

    private function scrapeArbeitnow()
    {
        $jobs = [];

        try {
            $response = Http::timeout(10)->get('https://www.arbeitnow.com/api/job-board-api');

            if ($response->successful()) {
                $data = $response->json();

                foreach (array_slice($data['data'] ?? [], 0, 2) as $job) {
                    $jobs[] = [
                        'external_id' => 'arbeitnow_' . $job['slug'],
                        'title' => $job['title'],
                        'company' => $job['company_name'] ?? 'Unknown Company',
                        'location' => $job['location'] ?? 'Remote',
                        'type' => $job['job_types'][0] ?? 'Full Time',
                        'description' => strip_tags($job['description'] ?? ''),
                        'salary' => null,
                        'tags' => json_encode($job['tags'] ?? []),
                        'posted_at' => Carbon::parse($job['created_at']),
                        'source' => 'Arbeitnow',
                        'url' => $job['url']
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->warn("Failed to scrape from Arbeitnow: " . $e->getMessage());
        }

        return $jobs;
    }
}
