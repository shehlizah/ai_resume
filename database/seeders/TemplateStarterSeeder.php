<?php

namespace Database\Seeders;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Facades\File;

class TemplateStarterSeeder extends Seeder
{
    public function xrun1()
    {
        $templates = [
            [
                'name' => 'Professional Blue',
                'slug' => Str::slug('Professional Blue'), // 👈 Add this line
                'category' => 'professional',
                'description' => 'Clean and professional template with blue accents. Perfect for corporate jobs.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 10,
                'features' => json_encode(['ats-friendly', 'single-page', 'photo-optional']),
                'html' => $this->getHtmlTemplate('professional_blue'),
                'css' => $this->getCssTemplate('professional_blue'),
            ],
            [
                'name' => 'Modern Gradient',
                 'slug' => Str::slug('modern'), // 👈 Add this line
                'category' => 'modern',
                'description' => 'Eye-catching design with gradient accents. Stand out from the crowd.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 20,
                'features' => json_encode(['colorful', 'two-column', 'photo-included']),
                'html' => $this->getHtmlTemplate('modern_gradient'),
                'css' => $this->getCssTemplate('modern_gradient'),
            ],
            [
                'name' => 'Minimal Black & White',
                 'slug' => Str::slug('minimal'), // 👈 Add this line
                'category' => 'minimal',
                'description' => 'Ultra-clean minimalist design. Works for any industry.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 30,
                'features' => json_encode(['ats-friendly', 'single-page', 'minimalist']),
                'html' => $this->getHtmlTemplate('minimal_bw'),
                'css' => $this->getCssTemplate('minimal_bw'),
            ],
            [
                'name' => 'Creative Portfolio',
                    'slug' => Str::slug('creative'), // 👈 Add this line

                'category' => 'creative',
                'description' => 'Bold and creative design for designers and creatives.',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 40,
                'features' => json_encode(['colorful', 'portfolio', 'photo-included']),
                'html' => $this->getHtmlTemplate('creative_portfolio'),
                'css' => $this->getCssTemplate('creative_portfolio'),
            ],
            [
                'name' => 'Executive Premium',
                    'slug' => Str::slug('executive'), // 👈 Add this line

                'category' => 'executive',
                'description' => 'Sophisticated design for senior executives and C-level positions.',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 50,
                'features' => json_encode(['ats-friendly', 'two-page', 'elegant']),
                'html' => $this->getHtmlTemplate('executive_premium'),
                'css' => $this->getCssTemplate('executive_premium'),
            ],
            [
                'name' => 'Tech Engineer',
                'slug' => Str::slug('modern'), // 👈 Add this line
                'category' => 'modern',
                'description' => 'Perfect for developers and tech professionals. Skills-focused layout.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 60,
                'features' => json_encode(['ats-friendly', 'skills-focused', 'tech-friendly']),
                'html' => $this->getHtmlTemplate('tech_engineer'),
                'css' => $this->getCssTemplate('tech_engineer'),
            ],
        ];

        foreach ($templates as $templateData) {
            // Create directories if they don't exist
            $templateDir = storage_path('app/templates');
            if (!File::exists($templateDir)) {
                File::makeDirectory($templateDir, 0755, true);
            }

            // Save HTML file using file_put_contents (doesn't need finfo)
            $htmlFilename = 'templates/' . str_replace(' ', '_', strtolower($templateData['name'])) . '_' . time() . '.html';
            $htmlFullPath = storage_path('app/' . $htmlFilename);
            file_put_contents($htmlFullPath, $templateData['html']);

            // Save CSS file
            $cssFilename = 'templates/' . str_replace(' ', '_', strtolower($templateData['name'])) . '_' . time() . '.css';
            $cssFullPath = storage_path('app/' . $cssFilename);
            file_put_contents($cssFullPath, $templateData['css']);

            // Create template record
            Template::create([
                'name' => $templateData['name'],
                'category' => $templateData['category'],
                'description' => $templateData['description'],
                'is_premium' => $templateData['is_premium'],
                'is_active' => $templateData['is_active'],
                'sort_order' => $templateData['sort_order'],
                'features' => $templateData['features'],
                'template_file' => $htmlFilename,
                'css_file' => $cssFilename,
                'preview_image' => null,
            ]);

            echo "✓ Created template: {$templateData['name']}\n";
        }

        echo "\n✅ Successfully seeded {count($templates)} templates!\n";
    }
    
    public function run()
    {
        $templates = [
            [
                'name' => 'Professional Blue',
                'slug' => Str::slug('Professional Blue'),
                'category' => 'professional',
                'description' => 'Clean and professional template with blue accents. Perfect for corporate jobs.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 10,
                'features' => ['ats-friendly', 'single-page', 'photo-optional'],
                'html_key' => 'professional_blue',
                'css_key' => 'professional_blue',
            ],
            [
                'name' => 'Modern Gradient',
                'slug' => Str::slug('Modern Gradient'),
                'category' => 'modern',
                'description' => 'Eye-catching design with gradient accents. Stand out from the crowd.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 20,
                'features' => ['colorful', 'two-column', 'photo-included'],
                'html_key' => 'modern_gradient',
                'css_key' => 'modern_gradient',
            ],
            [
                'name' => 'Minimal Black & White',
                'slug' => Str::slug('Minimal Black & White'),
                'category' => 'minimal',
                'description' => 'Ultra-clean minimalist design. Works for any industry.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 30,
                'features' => ['ats-friendly', 'single-page', 'minimalist'],
                'html_key' => 'minimal_bw',
                'css_key' => 'minimal_bw',
            ],
            [
                'name' => 'Creative Portfolio',
                'slug' => Str::slug('Creative Portfolio'),
                'category' => 'creative',
                'description' => 'Bold and creative design for designers and creatives.',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 40,
                'features' => ['colorful', 'portfolio', 'photo-included'],
                'html_key' => 'creative_portfolio',
                'css_key' => 'creative_portfolio',
            ],
            [
                'name' => 'Executive Premium',
                'slug' => Str::slug('Executive Premium'),
                'category' => 'executive',
                'description' => 'Sophisticated design for senior executives and C-level positions.',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 50,
                'features' => ['ats-friendly', 'two-page', 'elegant'],
                'html_key' => 'executive_premium',
                'css_key' => 'executive_premium',
            ],
            [
                'name' => 'Tech Engineer',
                'slug' => Str::slug('Tech Engineer'),
                'category' => 'modern',
                'description' => 'Perfect for developers and tech professionals. Skills-focused layout.',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 60,
                'features' => ['ats-friendly', 'skills-focused', 'tech-friendly'],
                'html_key' => 'tech_engineer',
                'css_key' => 'tech_engineer',
            ],
        ];

        $templateDir = storage_path('app/templates');
        if (!File::exists($templateDir)) {
            File::makeDirectory($templateDir, 0755, true);
        }

        foreach ($templates as $data) {
            $timestamp = time();

            // Create HTML and CSS files
            $htmlPath = "templates/" . Str::slug($data['name']) . "_{$timestamp}.html";
            $cssPath = "templates/" . Str::slug($data['name']) . "_{$timestamp}.css";

            File::put(storage_path("app/{$htmlPath}"), $this->getHtmlTemplate($data['html_key']));
            File::put(storage_path("app/{$cssPath}"), $this->getCssTemplate($data['css_key']));

            // Create DB record
            Template::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'category' => $data['category'],
                'description' => $data['description'],
                'is_premium' => $data['is_premium'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
                'features' => json_encode($data['features']),
                'template_file' => $htmlPath,
                'css_file' => $cssPath,
                'preview_image' => null,
            ]);

            $this->command->info("✓ Created template: {$data['name']}");
        }

        $this->command->info("✅ Successfully seeded " . count($templates) . " templates!");
    }

 
    private function getHtmlTemplate($type)
    {
        $templates = [
            'professional_blue' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{name}} - Resume</title>
</head>
<body>
    <div class="resume-container">
        <header class="header">
            <h1 class="name">{{name}}</h1>
            <p class="title">{{job_title}}</p>
            <div class="contact">
                <span>{{email}}</span> | <span>{{phone}}</span> | <span>{{location}}</span>
            </div>
        </header>

        <section class="summary">
            <h2>Professional Summary</h2>
            <p>{{summary}}</p>
        </section>

        <section class="experience">
            <h2>Work Experience</h2>
            {{#each work_experience}}
            <div class="job">
                <div class="job-header">
                    <h3>{{job_title}}</h3>
                    <span class="date">{{start_date}} - {{end_date}}</span>
                </div>
                <p class="company">{{company}}</p>
                <ul class="achievements">
                    {{#each responsibilities}}
                    <li>{{this}}</li>
                    {{/each}}
                </ul>
            </div>
            {{/each}}
        </section>

        <section class="education">
            <h2>Education</h2>
            {{#each education}}
            <div class="degree">
                <h3>{{degree}}</h3>
                <p>{{institution}} | {{graduation_year}}</p>
            </div>
            {{/each}}
        </section>

        <section class="skills">
            <h2>Skills</h2>
            <div class="skills-grid">
                {{#each skills}}
                <span class="skill-tag">{{this}}</span>
                {{/each}}
            </div>
        </section>
    </div>
</body>
</html>',

            'modern_gradient' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{name}} - Resume</title>
</head>
<body>
    <div class="resume-container">
        <div class="gradient-header">
            <img src="{{photo_url}}" alt="{{name}}" class="profile-photo">
            <div class="header-content">
                <h1 class="name">{{name}}</h1>
                <h2 class="title">{{job_title}}</h2>
                <div class="contact-modern">
                    <span>{{email}}</span>
                    <span>{{phone}}</span>
                    <span>{{location}}</span>
                </div>
            </div>
        </div>
        
        <div class="two-column">
            <aside class="sidebar">
                <div class="sidebar-section">
                    <h2>Skills</h2>
                    {{#each skills}}
                    <div class="skill-item">
                        <span>{{name}}</span>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: {{level}}%"></div>
                        </div>
                    </div>
                    {{/each}}
                </div>
            </aside>
            
            <main class="main-content">
                <section>
                    <h2>About Me</h2>
                    <p>{{summary}}</p>
                </section>
                
                <section>
                    <h2>Experience</h2>
                    {{#each work_experience}}
                    <div class="job-modern">
                        <h3>{{job_title}}</h3>
                        <p class="company-modern">{{company}} | {{start_date}} - {{end_date}}</p>
                        <ul>
                            {{#each responsibilities}}
                            <li>{{this}}</li>
                            {{/each}}
                        </ul>
                    </div>
                    {{/each}}
                </section>
            </main>
        </div>
    </div>
</body>
</html>',

            'minimal_bw' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{name}}</title>
</head>
<body>
    <div class="resume-container">
        <div class="minimal-header">
            <h1>{{name}}</h1>
            <p class="minimal-contact">{{email}} • {{phone}} • {{location}}</p>
        </div>
        
        <div class="minimal-section">
            <h2>Summary</h2>
            <p>{{summary}}</p>
        </div>
        
        <div class="minimal-section">
            <h2>Experience</h2>
            {{#each work_experience}}
            <div class="minimal-job">
                <div class="minimal-job-title">{{job_title}} at {{company}}</div>
                <div class="minimal-date">{{start_date}} - {{end_date}}</div>
                <ul class="minimal-list">
                    {{#each responsibilities}}
                    <li>{{this}}</li>
                    {{/each}}
                </ul>
            </div>
            {{/each}}
        </div>
        
        <div class="minimal-section">
            <h2>Education</h2>
            {{#each education}}
            <p><strong>{{degree}}</strong>, {{institution}} ({{graduation_year}})</p>
            {{/each}}
        </div>
        
        <div class="minimal-section">
            <h2>Skills</h2>
            <p class="minimal-skills">{{skills_list}}</p>
        </div>
    </div>
</body>
</html>',

            'creative_portfolio' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{name}} - Portfolio</title>
</head>
<body>
    <div class="resume-container">
        <aside class="creative-sidebar">
            <img src="{{photo_url}}" alt="{{name}}" class="creative-photo">
            <h1 class="creative-name">{{name}}</h1>
            <h2 class="creative-title">{{job_title}}</h2>
            
            <div class="creative-contact">
                <h3>Contact</h3>
                <p>{{email}}</p>
                <p>{{phone}}</p>
                <p>{{location}}</p>
            </div>
            
            <div class="creative-skills">
                <h3>Skills</h3>
                {{#each skills}}
                <div class="creative-skill">{{this}}</div>
                {{/each}}
            </div>
        </aside>
        
        <main class="creative-main">
            <section class="creative-section">
                <h2>About</h2>
                <p>{{summary}}</p>
            </section>
            
            <section class="creative-section">
                <h2>Experience</h2>
                {{#each work_experience}}
                <div class="creative-job">
                    <h3>{{job_title}}</h3>
                    <p class="creative-company">{{company}} | {{start_date}} - {{end_date}}</p>
                    <ul>
                        {{#each responsibilities}}
                        <li>{{this}}</li>
                        {{/each}}
                    </ul>
                </div>
                {{/each}}
            </section>
            
            <section class="creative-section">
                <h2>Projects</h2>
                {{#each projects}}
                <div class="creative-project">
                    <h3>{{title}}</h3>
                    <p>{{description}}</p>
                    <a href="{{url}}">View Project</a>
                </div>
                {{/each}}
            </section>
        </main>
    </div>
</body>
</html>',

            'executive_premium' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{name}} - Executive Resume</title>
</head>
<body>
    <div class="resume-container">
        <header class="executive-header">
            <h1 class="executive-name">{{name}}</h1>
            <h2 class="executive-title">{{job_title}}</h2>
            <div class="executive-contact">{{email}} • {{phone}} • {{location}}</div>
        </header>
        
        <section class="executive-section">
            <h3 class="executive-heading">Executive Summary</h3>
            <p class="executive-summary">{{summary}}</p>
        </section>
        
        <section class="executive-section">
            <h3 class="executive-heading">Core Competencies</h3>
            <div class="competencies-grid">
                {{#each skills}}
                <div class="competency">{{this}}</div>
                {{/each}}
            </div>
        </section>
        
        <section class="executive-section">
            <h3 class="executive-heading">Professional Experience</h3>
            {{#each work_experience}}
            <div class="executive-job">
                <div class="executive-job-header">
                    <h3>{{job_title}}</h3>
                    <span class="executive-dates">{{start_date}} - {{end_date}}</span>
                </div>
                <p class="executive-company">{{company}} | {{location}}</p>
                <ul class="executive-achievements">
                    {{#each responsibilities}}
                    <li>{{this}}</li>
                    {{/each}}
                </ul>
            </div>
            {{/each}}
        </section>
        
        <section class="executive-section">
            <h3 class="executive-heading">Education</h3>
            {{#each education}}
            <p><strong>{{degree}}</strong> - {{institution}}, {{graduation_year}}</p>
            {{/each}}
        </section>
    </div>
</body>
</html>',

            'tech_engineer' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{name}} - Tech Resume</title>
</head>
<body>
    <div class="resume-container">
        <header class="tech-header">
            <h1>{{name}}</h1>
            <p class="tech-role">{{job_title}}</p>
            <div class="tech-links">
                {{email}} | {{phone}} | <a href="{{github}}">GitHub</a> | <a href="{{linkedin}}">LinkedIn</a>
            </div>
        </header>
        
        <section class="tech-section">
            <h2>Technical Skills</h2>
            <div class="tech-skills-matrix">
                {{#each technical_skills}}
                <div class="tech-skill-category">
                    <strong>{{category}}:</strong> {{skills}}
                </div>
                {{/each}}
            </div>
        </section>
        
        <section class="tech-section">
            <h2>Work Experience</h2>
            {{#each work_experience}}
            <div class="tech-job">
                <div class="tech-job-header">
                    <h3>{{job_title}} @ {{company}}</h3>
                    <span class="tech-date">{{start_date}} - {{end_date}}</span>
                </div>
                <p class="tech-stack">Tech Stack: {{tech_stack}}</p>
                <ul>
                    {{#each responsibilities}}
                    <li>{{this}}</li>
                    {{/each}}
                </ul>
            </div>
            {{/each}}
        </section>
        
        <section class="tech-section">
            <h2>Projects</h2>
            {{#each projects}}
            <div class="tech-project">
                <h3>{{name}}</h3>
                <p>{{description}}</p>
                <p><strong>Technologies:</strong> {{technologies}}</p>
                <a href="{{url}}">View Project</a>
            </div>
            {{/each}}
        </section>
    </div>
</body>
</html>',
        ];

        return $templates[$type] ?? '';
    }

    private function getCssTemplate($type)
    {
        $styles = [
            'professional_blue' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Calibri", Arial, sans-serif; line-height: 1.6; color: #333; }
.resume-container { max-width: 800px; margin: 0 auto; padding: 40px; background: white; }
.header { text-align: center; padding-bottom: 20px; border-bottom: 3px solid #2563eb; margin-bottom: 30px; }
.name { font-size: 32px; font-weight: bold; color: #1e40af; margin-bottom: 5px; }
.title { font-size: 18px; color: #64748b; margin-bottom: 10px; }
.contact { font-size: 14px; color: #64748b; }
h2 { color: #1e40af; font-size: 20px; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0; }
.summary p { text-align: justify; margin-bottom: 20px; }
.job { margin-bottom: 25px; }
.job-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.job-header h3 { font-size: 16px; color: #1e293b; }
.date { font-size: 14px; color: #64748b; }
.company { color: #475569; margin-bottom: 10px; font-style: italic; }
.achievements { padding-left: 20px; }
.achievements li { margin-bottom: 8px; color: #334155; }
.skills-grid { display: flex; flex-wrap: wrap; gap: 10px; }
.skill-tag { background: #dbeafe; color: #1e40af; padding: 5px 15px; border-radius: 20px; font-size: 14px; }
.degree { margin-bottom: 15px; }
.degree h3 { font-size: 16px; color: #1e293b; margin-bottom: 3px; }',

            'modern_gradient' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Inter", "Segoe UI", sans-serif; line-height: 1.6; color: #1f2937; }
.resume-container { max-width: 900px; margin: 0 auto; background: white; }
.gradient-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; display: flex; align-items: center; gap: 30px; }
.profile-photo { width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; object-fit: cover; }
.header-content { flex: 1; }
.name { font-size: 36px; font-weight: bold; margin-bottom: 5px; }
.title { font-size: 20px; opacity: 0.9; margin-bottom: 15px; }
.contact-modern { font-size: 14px; display: flex; gap: 20px; opacity: 0.95; }
.two-column { display: grid; grid-template-columns: 300px 1fr; }
.sidebar { background: #f9fafb; padding: 30px; }
.main-content { padding: 30px 40px; }
h2 { color: #667eea; font-size: 18px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
.skill-item { margin-bottom: 15px; }
.skill-bar { background: #e5e7eb; height: 6px; border-radius: 3px; margin-top: 5px; }
.skill-level { background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; border-radius: 3px; }
.job-modern { margin-bottom: 30px; }
.job-modern h3 { font-size: 18px; color: #1f2937; margin-bottom: 5px; }
.company-modern { color: #6b7280; margin-bottom: 10px; }
.job-modern ul { padding-left: 20px; }
.job-modern li { margin-bottom: 8px; }',

            'minimal_bw' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Times New Roman", serif; line-height: 1.8; color: #000; background: white; }
.resume-container { max-width: 750px; margin: 0 auto; padding: 50px 40px; }
.minimal-header { text-align: center; margin-bottom: 40px; }
.minimal-header h1 { font-size: 28px; font-weight: normal; letter-spacing: 2px; margin-bottom: 10px; }
.minimal-contact { font-size: 12px; letter-spacing: 0.5px; }
.minimal-section { margin-bottom: 35px; }
.minimal-section h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px; }
.minimal-job { margin-bottom: 20px; }
.minimal-job-title { font-weight: bold; margin-bottom: 3px; }
.minimal-date { font-size: 12px; color: #555; margin-bottom: 8px; }
.minimal-list { list-style-position: outside; padding-left: 20px; }
.minimal-list li { margin-bottom: 6px; font-size: 14px; }
.minimal-skills { font-size: 14px; }',

            'creative_portfolio' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Poppins", sans-serif; color: #2d3748; }
.resume-container { display: grid; grid-template-columns: 300px 1fr; min-height: 100vh; }
.creative-sidebar { background: linear-gradient(180deg, #ff6b6b 0%, #ee5a6f 100%); color: white; padding: 40px 30px; }
.creative-photo { width: 150px; height: 150px; border-radius: 50%; border: 5px solid white; margin-bottom: 20px; object-fit: cover; }
.creative-name { font-size: 26px; font-weight: bold; margin-bottom: 5px; }
.creative-title { font-size: 16px; opacity: 0.9; margin-bottom: 30px; }
.creative-contact, .creative-skills { margin-bottom: 30px; }
.creative-contact h3, .creative-skills h3 { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
.creative-skill { background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; margin-bottom: 10px; font-size: 14px; }
.creative-main { padding: 50px 40px; }
.creative-section { margin-bottom: 40px; }
.creative-section h2 { color: #ff6b6b; font-size: 24px; margin-bottom: 20px; }
.creative-job { margin-bottom: 30px; }
.creative-job h3 { font-size: 18px; color: #2d3748; margin-bottom: 5px; }
.creative-company { color: #718096; margin-bottom: 10px; }
.creative-project { border-left: 3px solid #ff6b6b; padding-left: 20px; margin-bottom: 25px; }
.creative-project a { color: #ff6b6b; text-decoration: none; font-weight: 600; }',

            'executive_premium' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Garamond", "Georgia", serif; line-height: 1.7; color: #1a202c; }
.resume-container { max-width: 850px; margin: 0 auto; padding: 60px 50px; background: white; }
.executive-header { text-align: center; margin-bottom: 40px; padding-bottom: 25px; border-bottom: 2px solid #2d3748; }
.executive-name { font-size: 38px; font-weight: normal; letter-spacing: 3px; margin-bottom: 8px; }
.executive-title { font-size: 20px; color: #4a5568; font-style: italic; margin-bottom: 15px; }
.executive-contact { font-size: 13px; color: #718096; letter-spacing: 0.5px; }
.executive-section { margin-bottom: 35px; }
.executive-heading { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; color: #2d3748; margin-bottom: 20px; border-bottom: 1px solid #cbd5e0; padding-bottom: 8px; }
.executive-summary { text-align: justify; font-size: 15px; line-height: 1.8; }
.competencies-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.competency { background: #f7fafc; padding: 10px 15px; text-align: center; border-radius: 5px; font-size: 14px; }
.executive-job { margin-bottom: 30px; }
.executive-job-header { display: flex; justify-content: space-between; margin-bottom: 12px; }
.executive-job-header h3 { font-size: 17px; font-weight: 600; color: #1a202c; }
.executive-company { color: #4a5568; font-style: italic; }
.executive-dates { color: #718096; font-size: 14px; }
.executive-achievements { padding-left: 25px; }
.executive-achievements li { margin-bottom: 10px; }',

            'tech_engineer' => '* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "Roboto Mono", "Consolas", monospace; line-height: 1.6; color: #0f172a; background: #f8fafc; }
.resume-container { max-width: 900px; margin: 0 auto; padding: 40px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.tech-header { border-left: 4px solid #0ea5e9; padding-left: 20px; margin-bottom: 30px; }
.tech-header h1 { font-size: 30px; font-weight: bold; color: #0f172a; margin-bottom: 5px; }
.tech-role { font-size: 18px; color: #64748b; margin-bottom: 10px; }
.tech-links { font-size: 13px; color: #475569; }
.tech-links a { color: #0ea5e9; text-decoration: none; }
.tech-section { margin-bottom: 30px; }
.tech-section h2 { font-size: 16px; text-transform: uppercase; letter-spacing: 1px; color: #0ea5e9; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
.tech-skills-matrix { display: flex; flex-direction: column; gap: 10px; background: #f1f5f9; padding: 20px; border-radius: 8px; }
.tech-skill-category { font-size: 14px; }
.tech-skill-category strong { color: #0ea5e9; }
.tech-job { margin-bottom: 25px; border-left: 2px solid #e2e8f0; padding-left: 15px; }
.tech-job-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.tech-job-header h3 { font-size: 16px; color: #0f172a; }
.tech-date { font-size: 13px; color: #64748b; }
.tech-stack { font-size: 13px; color: #0ea5e9; margin-bottom: 10px; font-style: italic; }
.tech-job ul { padding-left: 20px; }
.tech-job li { margin-bottom: 8px; font-size: 14px; }
.tech-project { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid #0ea5e9; }
.tech-project h3 { font-size: 16px; margin-bottom: 8px; color: #0f172a; }
.tech-project a { color: #0ea5e9; text-decoration: none; font-weight: 600; }',
        ];

        return $styles[$type] ?? '';
    }
}