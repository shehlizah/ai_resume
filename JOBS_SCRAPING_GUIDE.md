# Job Scraping Setup Guide

## Overview
This system scrapes jobs from 3 free, open-source job boards:
1. **Remotive** - Remote jobs API (no auth required)
2. **RemoteOK** - Popular remote job board
3. **Arbeitnow** - European job board with free API

## Setup Instructions

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Scrape Jobs Manually
```bash
# Scrape default 7 jobs
php artisan jobs:scrape

# Scrape custom amount
php artisan jobs:scrape --limit=10
```

### 3. Schedule Automatic Scraping (Optional)
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Scrape jobs every 6 hours
    $schedule->command('jobs:scrape')->everySixHours();
}
```

Then run the scheduler:
```bash
php artisan schedule:work
```

### 4. Add Routes
Add to `routes/web.php` or `routes/api.php`:

```php
use App\Http\Controllers\JobController;

Route::get('/api/jobs', [JobController::class, 'index']);
Route::get('/api/jobs/{job}', [JobController::class, 'show']);
```

## API Endpoints

### Get Jobs List
```
GET /api/jobs
```

Response:
```json
{
  "jobs": [
    {
      "id": 1,
      "title": "Senior Software Engineer",
      "company": "Tech Corp",
      "location": "Remote",
      "type": "Full Time",
      "tags": ["PHP", "Laravel", "Remote"],
      "time_ago": "2 hours ago",
      "source": "Remotive",
      "url": "https://..."
    }
  ],
  "total": 364
}
```

## Update Landing Page to Use Real Jobs

Replace the static job cards in `landing-page-complete.html` with:

```javascript
<script>
// Fetch and display real jobs
fetch('/api/jobs')
  .then(response => response.json())
  .then(data => {
    const jobsGrid = document.querySelector('.jobs-grid');
    const jobsCount = document.querySelector('.jobs-count');
    
    // Update count
    jobsCount.textContent = data.total;
    
    // Clear existing jobs
    jobsGrid.innerHTML = '';
    
    // Add real jobs
    data.jobs.data.forEach(job => {
      const jobCard = `
        <div class="job-card">
          <div class="job-logo">${getJobEmoji(job.tags)}</div>
          <div class="job-info">
            <div style="display: flex; justify-content: space-between; align-items: start;">
              <div>
                <h3 class="job-title">${job.title}</h3>
                <p class="job-company">${job.company}</p>
              </div>
              ${job.is_featured ? '<div class="job-badge">Featured</div>' : ''}
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #8492A6; margin-bottom: 1rem;">
              <span>📍</span>
              <span>${job.location}</span>
            </div>
            <div class="job-tags">
              ${job.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
            </div>
          </div>
        </div>
      `;
      jobsGrid.innerHTML += jobCard;
    });
  });

function getJobEmoji(tags) {
  if (tags.includes('Senior') || tags.includes('Lead')) return '🚀';
  if (tags.includes('Frontend') || tags.includes('React')) return '💻';
  if (tags.includes('Backend') || tags.includes('Node')) return '⚙️';
  return '💼';
}
</script>
```

## Job Sources

### 1. Remotive (remotive.com)
- **API**: Public, no auth
- **Rate Limit**: ~100 requests/hour
- **Jobs**: Remote tech jobs worldwide

### 2. RemoteOK (remoteok.com)
- **API**: Public, no auth
- **Rate Limit**: Be respectful, cache results
- **Jobs**: Global remote jobs

### 3. Arbeitnow (arbeitnow.com)
- **API**: Free, no auth required
- **Jobs**: European market focused

## Customization

### Add More Sources
Edit `app/Console/Commands/ScrapeJobs.php` and add:

```php
private function scrapeYourSource()
{
    $jobs = [];
    
    try {
        $response = Http::get('https://api.example.com/jobs');
        
        foreach ($response->json() as $job) {
            $jobs[] = [
                'external_id' => 'source_' . $job['id'],
                'title' => $job['title'],
                'company' => $job['company'],
                'location' => $job['location'],
                'type' => 'Full Time',
                'description' => $job['description'],
                'salary' => $job['salary'] ?? null,
                'tags' => json_encode($job['skills']),
                'posted_at' => now(),
                'source' => 'YourSource',
                'url' => $job['url']
            ];
        }
    } catch (\Exception $e) {
        $this->warn("Failed: " . $e->getMessage());
    }
    
    return $jobs;
}
```

Then call it in `handle()` method:
```php
$jobs = array_merge($jobs, $this->scrapeYourSource());
```

## Troubleshooting

### Jobs not appearing?
```bash
php artisan jobs:scrape --limit=7
php artisan tinker
>>> App\Models\Job::count()
```

### API timeout?
Increase timeout in scraping methods:
```php
Http::timeout(30)->get('...')
```

### Want to clear old jobs?
```bash
php artisan tinker
>>> App\Models\Job::truncate()
```

## Notes
- All sources are legal and free to use
- Respect rate limits (we cache and limit requests)
- Jobs are updated every 6 hours (if scheduled)
- Scraping follows robots.txt and terms of service
