<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddOn;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $addOns = [
            [
                'name' => 'Job Links Directory',
                'slug' => 'job-links-directory',
                'description' => 'Get access to verified job links across multiple platforms. Find relevant job openings tailored to your resume.',
                'price' => 20.00,
                'type' => 'job_links',
                'is_active' => true,
                'features' => [
                    'Access to 100+ job boards',
                    'Verified job listings',
                    'Direct application links',
                    'Industry-specific job sites',
                    'Remote job opportunities',
                    'Lifetime access',
                ],
                'content' => [
                    'job_sites' => [
                        ['name' => 'LinkedIn Jobs', 'url' => 'https://www.linkedin.com/jobs/', 'category' => 'General'],
                        ['name' => 'Indeed', 'url' => 'https://www.indeed.com/', 'category' => 'General'],
                        ['name' => 'Glassdoor', 'url' => 'https://www.glassdoor.com/Job/', 'category' => 'General'],
                        ['name' => 'Monster', 'url' => 'https://www.monster.com/', 'category' => 'General'],
                        ['name' => 'ZipRecruiter', 'url' => 'https://www.ziprecruiter.com/', 'category' => 'General'],
                        ['name' => 'AngelList', 'url' => 'https://angel.co/jobs', 'category' => 'Startups'],
                        ['name' => 'We Work Remotely', 'url' => 'https://weworkremotely.com/', 'category' => 'Remote'],
                        ['name' => 'Remote.co', 'url' => 'https://remote.co/remote-jobs/', 'category' => 'Remote'],
                        ['name' => 'FlexJobs', 'url' => 'https://www.flexjobs.com/', 'category' => 'Remote'],
                        ['name' => 'Stack Overflow Jobs', 'url' => 'https://stackoverflow.com/jobs', 'category' => 'Tech'],
                    ],
                ],
                'sort_order' => 1,
                'icon' => 'bx-briefcase',
            ],
            [
                'name' => 'Interview Preparation Kit',
                'slug' => 'interview-preparation-kit',
                'description' => 'Master your interviews with our comprehensive preparation kit. Includes common questions, tips, and strategies.',
                'price' => 20.00,
                'type' => 'interview_prep',
                'is_active' => true,
                'features' => [
                    '500+ interview questions',
                    'Answer templates & examples',
                    'STAR method guides',
                    'Behavioral interview tips',
                    'Technical interview prep',
                    'Video interview strategies',
                    'Salary negotiation guide',
                    'Follow-up email templates',
                ],
                'content' => [
                    'resources' => [
                        [
                            'title' => 'Common Interview Questions',
                            'type' => 'questions',
                            'items' => [
                                'Tell me about yourself',
                                'What are your strengths and weaknesses?',
                                'Why do you want to work here?',
                                'Where do you see yourself in 5 years?',
                                'Why should we hire you?',
                            ],
                        ],
                        [
                            'title' => 'STAR Method Guide',
                            'type' => 'guide',
                            'description' => 'Situation, Task, Action, Result - A proven framework for answering behavioral questions.',
                        ],
                        [
                            'title' => 'Salary Negotiation Tips',
                            'type' => 'guide',
                            'description' => 'Learn how to negotiate your salary effectively and get the compensation you deserve.',
                        ],
                    ],
                    'external_resources' => [
                        ['name' => 'Glassdoor Interview Tips', 'url' => 'https://www.glassdoor.com/blog/guide/interview-tips/'],
                        ['name' => 'LinkedIn Interview Prep', 'url' => 'https://www.linkedin.com/interview-prep/'],
                        ['name' => 'The Muse Career Advice', 'url' => 'https://www.themuse.com/advice/interview'],
                    ],
                ],
                'sort_order' => 2,
                'icon' => 'bx-user-voice',
            ],
        ];

        foreach ($addOns as $addOn) {
            AddOn::create($addOn);
        }
    }
}