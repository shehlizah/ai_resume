/**
 * JobSease - Direct Job Scraping (No Database)
 * Fetches jobs from free APIs and displays them directly on the frontend
 */

const JobLoader = {
    // API endpoints
    apis: {
        remotive: 'https://remotive.com/api/remote-jobs?category=software-dev&limit=3',
        remoteok: 'https://remoteok.com/api',
        arbeitnow: 'https://www.arbeitnow.com/api/job-board-api'
    },

    // Store fetched jobs
    jobs: [],
    
    // Maximum number of jobs to display
    maxJobs: 7,

    /**
     * Initialize and load jobs
     */
    async init() {
        console.log('🚀 Loading jobs from multiple sources...');
        
        try {
            // Fetch from all sources in parallel
            const [remotiveJobs, remoteokJobs, arbeitnowJobs] = await Promise.allSettled([
                this.fetchRemotive(),
                this.fetchRemoteOK(),
                this.fetchArbeitnow()
            ]);

            // Combine all successful results
            if (remotiveJobs.status === 'fulfilled') this.jobs.push(...remotiveJobs.value);
            if (remoteokJobs.status === 'fulfilled') this.jobs.push(...remoteokJobs.value);
            if (arbeitnowJobs.status === 'fulfilled') this.jobs.push(...arbeitnowJobs.value);

            // Limit to max jobs
            this.jobs = this.jobs.slice(0, this.maxJobs);

            console.log(`✅ Loaded ${this.jobs.length} jobs`);
            
            // Display jobs
            this.displayJobs();
            this.updateJobsCount();
            
        } catch (error) {
            console.error('❌ Error loading jobs:', error);
            this.showError();
        }
    },

    /**
     * Fetch jobs from Remotive API
     */
    async fetchRemotive() {
        try {
            const response = await fetch(this.apis.remotive);
            const data = await response.json();
            
            return data.jobs.slice(0, 3).map(job => ({
                id: `remotive-${job.id}`,
                title: job.title,
                company: job.company_name,
                location: job.candidate_required_location || 'Remote',
                type: 'Full Time',
                description: this.stripHtml(job.description),
                salary: job.salary || null,
                tags: ['Remote', 'Software Development'],
                posted_at: new Date(job.publication_date),
                source: 'Remotive',
                url: job.url,
                logo: this.getCompanyEmoji(job.company_name)
            }));
        } catch (error) {
            console.warn('⚠️ Failed to fetch from Remotive:', error.message);
            return [];
        }
    },

    /**
     * Fetch jobs from RemoteOK API
     */
    async fetchRemoteOK() {
        try {
            const response = await fetch(this.apis.remoteok, {
                headers: {
                    'User-Agent': 'JobSease/1.0'
                }
            });
            const data = await response.json();
            
            // RemoteOK returns metadata as first item, skip it
            return data.slice(1, 3).map(job => ({
                id: `remoteok-${job.id}`,
                title: job.position,
                company: job.company,
                location: job.location || 'Remote',
                type: 'Full Time',
                description: this.stripHtml(job.description),
                salary: job.salary_min ? `$${job.salary_min}+` : null,
                tags: job.tags ? job.tags.slice(0, 3) : [],
                posted_at: new Date(job.date * 1000),
                source: 'RemoteOK',
                url: job.url,
                logo: this.getCompanyEmoji(job.company)
            }));
        } catch (error) {
            console.warn('⚠️ Failed to fetch from RemoteOK:', error.message);
            return [];
        }
    },

    /**
     * Fetch jobs from Arbeitnow API
     */
    async fetchArbeitnow() {
        try {
            const response = await fetch(this.apis.arbeitnow);
            const data = await response.json();
            
            return data.data.slice(0, 2).map(job => ({
                id: `arbeitnow-${job.slug}`,
                title: job.title,
                company: job.company_name,
                location: job.location,
                type: job.job_types?.[0] || 'Full Time',
                description: this.stripHtml(job.description),
                salary: null,
                tags: job.tags || [],
                posted_at: new Date(job.created_at * 1000),
                source: 'Arbeitnow',
                url: job.url,
                logo: this.getCompanyEmoji(job.company_name)
            }));
        } catch (error) {
            console.warn('⚠️ Failed to fetch from Arbeitnow:', error.message);
            return [];
        }
    },

    /**
     * Display jobs in the DOM
     */
    displayJobs() {
        const jobsGrid = document.querySelector('.jobs-grid');
        if (!jobsGrid) {
            console.error('❌ Jobs grid container not found');
            return;
        }

        // Clear existing jobs
        jobsGrid.innerHTML = '';

        // Create job cards
        this.jobs.forEach((job, index) => {
            const isNew = this.isNewJob(job.posted_at);
            const isFeatured = index === 0; // Make first job featured
            
            jobsGrid.innerHTML += `
                <div class="job-card" data-job-id="${job.id}">
                    <div class="job-logo">${job.logo}</div>
                    <div class="job-info">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h3 class="job-title">${job.title}</h3>
                                <p class="job-company">${job.company}</p>
                            </div>
                            ${isNew ? '<div class="job-badge">New</div>' : `<span style="color: #8492A6; font-size: 0.875rem;">${this.getTimeAgo(job.posted_at)}</span>`}
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #8492A6; margin-bottom: 1rem;">
                            <span>📍</span>
                            <span>${job.location}</span>
                        </div>
                        <div class="job-tags">
                            ${isFeatured ? '<span class="tag featured">Featured</span>' : ''}
                            ${job.tags.slice(0, 3).map(tag => `<span class="tag">${tag}</span>`).join('')}
                            ${job.type ? `<span class="tag">${job.type}</span>` : ''}
                            ${job.salary ? `<span class="tag">💰 ${job.salary}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        // Add click handlers to open job links
        document.querySelectorAll('.job-card').forEach(card => {
            const jobId = card.getAttribute('data-job-id');
            const job = this.jobs.find(j => j.id === jobId);
            
            if (job && job.url) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => {
                    window.open(job.url, '_blank');
                });
            }
        });
    },

    /**
     * Update jobs count display
     */
    updateJobsCount() {
        const countElement = document.querySelector('.jobs-count');
        if (countElement) {
            countElement.textContent = this.jobs.length;
        }
    },

    /**
     * Show error message
     */
    showError() {
        const jobsGrid = document.querySelector('.jobs-grid');
        if (jobsGrid) {
            jobsGrid.innerHTML = `
                <div style="text-align: center; padding: 4rem 2rem; color: #8492A6;">
                    <p style="font-size: 1.25rem; margin-bottom: 1rem;">⚠️ Unable to load jobs at the moment</p>
                    <p>Please try again later or check your internet connection.</p>
                    <button onclick="location.reload()" class="btn btn-primary" style="margin-top: 2rem;">
                        Retry
                    </button>
                </div>
            `;
        }
    },

    /**
     * Strip HTML tags from string
     */
    stripHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    },

    /**
     * Get company emoji based on company name or tags
     */
    getCompanyEmoji(company) {
        const emojis = ['💼', '💻', '🚀', '⚙️', '🎯', '📱', '🔧', '🌐'];
        const index = company.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % emojis.length;
        return emojis[index];
    },

    /**
     * Check if job is new (posted within last 24 hours)
     */
    isNewJob(postedDate) {
        const dayInMs = 24 * 60 * 60 * 1000;
        return (Date.now() - postedDate.getTime()) < dayInMs;
    },

    /**
     * Get human-readable time ago
     */
    getTimeAgo(date) {
        const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
        
        const intervals = {
            year: 31536000,
            month: 2592000,
            week: 604800,
            day: 86400,
            hour: 3600,
            minute: 60
        };

        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInUnit);
            if (interval >= 1) {
                return `${interval}${unit.charAt(0)}`;
            }
        }

        return 'now';
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => JobLoader.init());
} else {
    JobLoader.init();
}
