<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume - {{ $resume->data['name'] ?? 'Candidate' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Calibri', 'Arial', sans-serif; 
            background: #ffffff; 
            color: #333; 
            line-height: 1.6;
            font-size: 11pt;
        }
        .resume-container { 
            max-width: 8.5in; 
            margin: 0 auto; 
            padding: 0.75in; 
            background: #fff;
        }
        
        /* Header Section */
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 12px; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            font-size: 24pt; 
            font-weight: bold; 
            color: #000; 
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .title { 
            font-size: 12pt; 
            color: #444; 
            margin-bottom: 8px;
            font-weight: 600;
        }
        .header .contact-info { 
            font-size: 10pt; 
            color: #555; 
        }
        .header .contact-info span { 
            margin: 0 8px; 
        }
        .score-badge {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 4px 12px;
            font-size: 9pt;
            margin-top: 6px;
            font-weight: bold;
        }
        
        /* Section Headings */
        .section { 
            margin-bottom: 18px; 
        }
        .section-title { 
            font-size: 13pt; 
            font-weight: bold; 
            color: #000; 
            text-transform: uppercase; 
            border-bottom: 1.5px solid #333; 
            padding-bottom: 3px; 
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        
        /* Summary */
        .summary-text { 
            text-align: justify; 
            color: #333;
            margin-bottom: 4px;
        }
        
        /* Skills - Grid Layout */
        .skill-category-heading {
            font-size: 10.5pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            margin-top: 8px;
        }
        .skills-grid { 
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px 12px;
            margin-top: 4px;
        }
        .skill-item { 
            font-size: 10pt; 
            color: #333;
            padding: 3px 0;
        }
        .skill-item:before {
            content: "• ";
            font-weight: bold;
        }
        
        /* Experience */
        .experience-item { 
            margin-bottom: 20px; 
            page-break-inside: avoid;
        }
        .job-title { 
            font-size: 11pt; 
            font-weight: bold; 
            color: #000;
            margin-bottom: 4px;
        }
        .job-date { 
            font-size: 10pt; 
            color: #555; 
            font-style: italic;
            margin-bottom: 4px;
        }
        .company-name { 
            font-size: 10pt; 
            color: #444; 
            margin-bottom: 8px;
            font-weight: 600;
        }
        .responsibilities-heading {
            font-size: 10pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 4px;
        }
        .job-description { 
            font-size: 10pt; 
            color: #333; 
            margin-left: 0;
            text-align: justify;
        }
        .job-description ul {
            margin: 0 0 0 20px;
            padding: 0;
        }
        .job-description li {
            margin-bottom: 3px;
        }
        
        /* Education */
        .education-item { 
            margin-bottom: 10px; 
        }
        .education-degree { 
            font-size: 11pt; 
            font-weight: bold; 
            color: #000; 
        }
        .education-institution { 
            font-size: 10pt; 
            color: #444; 
        }
        .education-year { 
            font-size: 10pt; 
            color: #555; 
            font-style: italic;
        }
        
        /* Projects */
        .project-item { 
            margin-bottom: 10px; 
        }
        .project-title { 
            font-size: 11pt; 
            font-weight: bold; 
            color: #000; 
        }
        .project-description { 
            font-size: 10pt; 
            color: #333; 
            margin-top: 2px;
        }
        
        /* Print Styles */
        @media print {
            body { background: #fff; }
            .resume-container { 
                max-width: 100%; 
                padding: 0.5in;
            }
            @page { margin: 0.5in; }
        }
    </style>
</head>
<body>
@php
    // Normalize resume data
    $raw = $resume->data ?? [];
    $data = is_string($raw) ? (json_decode($raw, true) ?: []) : ($raw ?? []);

    // Helper: clean HTML
    $cleanHtml = function($value) {
        if (!$value) return '';
        $cleaned = strip_tags($value);
        $cleaned = html_entity_decode($cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return trim($cleaned);
    };

    // Helper: convert to array
    $toArray = function ($value) use ($cleanHtml) {
        if (is_array($value)) return array_values(array_filter(array_map($cleanHtml, $value), fn($v) => $v !== ''));
        if (is_string($value)) {
            $value = $cleanHtml($value);
            return array_values(array_filter(preg_split('/[,;\n]+/', $value), fn($v) => trim($v) !== ''));
        }
        return [];
    };

    // Helper: parse responsibilities from description
    $parseResponsibilities = function($description) use ($cleanHtml) {
        $cleaned = $cleanHtml($description);
        // Split by newlines, bullets, or numbered lists
        $parts = preg_split('/[\n\r]+|(?=\d+\.)|(?=[-•])/', $cleaned);
        $responsibilities = [];
        foreach($parts as $part) {
            $part = trim(preg_replace('/^[\d\.\-•\s]+/', '', $part));
            if ($part && strlen($part) > 5) {
                $responsibilities[] = $part;
            }
        }
        return $responsibilities;
    };

    $name = $data['name'] ?? 'Candidate';
    $title = $data['title'] ?? $data['headline'] ?? 'Professional';
    $location = $data['location'] ?? $data['city'] ?? null;
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;
    $summary = $cleanHtml($data['summary'] ?? $data['objective'] ?? '');

    // Parse skills - detect category headings
    $rawSkills = $data['skills'] ?? [];
    $skillsData = [];
    if (is_string($rawSkills)) {
        // Parse string that might contain categories
        $rawSkills = $cleanHtml($rawSkills);
        // First check if there's a category prefix like "Technical Skills: PHP, WordPress..."
        if (preg_match('/^([^:]+Skills[^:]*):(.+)$/i', $rawSkills, $matches)) {
            $category = trim($matches[1]);
            $skillsList = $matches[2];
            $items = preg_split('/[,;]+/', $skillsList);
            foreach($items as $item) {
                $item = trim($item);
                if ($item) {
                    $skillsData[$category][] = $item;
                }
            }
        } else {
            // Try line-by-line parsing
            $lines = preg_split('/[\n\r]+/', $rawSkills);
            $currentCategory = null;
            foreach($lines as $line) {
                $line = trim($line);
                if (!$line) continue;
                // Check if line has category prefix
                if (preg_match('/^([^:]+Skills[^:]*):(.+)$/i', $line, $matches)) {
                    $currentCategory = trim($matches[1]);
                    $skillsList = $matches[2];
                    $items = preg_split('/[,;]+/', $skillsList);
                    foreach($items as $item) {
                        $item = trim($item);
                        if ($item) {
                            $skillsData[$currentCategory][] = $item;
                        }
                    }
                } elseif (preg_match('/(Skills|Competencies):?$/i', $line)) {
                    $currentCategory = $line;
                    $skillsData[$currentCategory] = [];
                } else {
                    // It's a skill item
                    $items = preg_split('/[,;]+/', $line);
                    foreach($items as $item) {
                        $item = trim($item);
                        if ($item) {
                            if ($currentCategory) {
                                $skillsData[$currentCategory][] = $item;
                            } else {
                                $skillsData[''][] = $item;
                            }
                        }
                    }
                }
            }
        }
    } elseif (is_array($rawSkills)) {
        $skillsData[''] = array_values(array_filter(array_map($cleanHtml, $rawSkills), fn($v) => $v !== ''));
    }
    
    // Handle experience
    $rawExperience = $data['experience'] ?? $data['job_title'] ?? [];
    if (is_string($rawExperience)) {
        $cleanExp = $cleanHtml($rawExperience);
        $expParts = preg_split('/\n{2,}/', $cleanExp);
        $experiences = array_filter($expParts, fn($v) => trim($v) !== '');
    } else {
        $experiences = $rawExperience;
    }
    
    $education = $data['education'] ?? [];
    $projects = $data['projects'] ?? [];
@endphp

<div class="resume-container">
    <!-- Header -->
    <div class="header">
        <h1>{{ $name }}</h1>
        <div class="title">{{ $title }}</div>
        @if($location || $email || $phone)
            <div class="contact-info">
                @if($email)<span>{{ $email }}</span>@endif
                @if($phone)<span>|</span><span>{{ $phone }}</span>@endif
                @if($location)<span>|</span><span>{{ $location }}</span>@endif
            </div>
        @endif
        @if(!empty($resume->score))
            <div class="score-badge">Match Score: {{ $resume->score }}%</div>
        @endif
    </div>

    <!-- Summary -->
    @if($summary)
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <div class="summary-text">{{ $summary }}</div>
        </div>
    @endif

    <!-- Skills -->
    @if(!empty($skillsData))
        <div class="section">
            <div class="section-title">Core Competencies</div>
            @foreach($skillsData as $category => $skillsList)
                @if(!empty($skillsList))
                    @if($category)
                        <div style="font-weight: 600; margin-top: 12px; margin-bottom: 8px; color: #2c3e50; font-size: 10.5pt; clear: both;">{{ $category }}</div>
                    @endif
                    <div style="display: block; margin-bottom: 12px; clear: both;">
                        @foreach($skillsList as $skill)
                            @if($skill)
                                <div style="float: left; width: 33.33%; font-size: 9.5pt; padding: 2px 0; box-sizing: border-box;">• {{ $skill }}</div>
                            @endif
                        @endforeach
                        <div style="clear: both;"></div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Experience -->
    @if(!empty($experiences))
        <div class="section">
            <div class="section-title">Professional Experience</div>
            @foreach($experiences as $exp)
                <div style="border-left: 3px solid #e0e0e0; padding-left: 12px; margin-bottom: 24px; clear: both; page-break-inside: avoid;">
                    @if(is_array($exp))
                        <div style="font-weight: 600; color: #2c3e50; font-size: 11pt; clear: both;">{{ $cleanHtml($exp['title'] ?? $exp['role'] ?? $exp['position'] ?? 'Position') }}</div>
                        <br>
                        @if(!empty($exp['from']) || !empty($exp['to']))
                            <div style="color: #7f8c8d; font-size: 9.5pt; clear: both;">{{ ($exp['from'] ?? '') }} - {{ ($exp['to'] ?? 'Present') }}</div>
                            <br>
                        @endif
                        @if(!empty($exp['company']))
                            <div style="color: #34495e; font-style: italic; clear: both;">{{ $cleanHtml($exp['company']) }}</div>
                            <br>
                        @endif
                        @if(!empty($exp['description']))
                            @php
                                $responsibilities = $parseResponsibilities($exp['description']);
                            @endphp
                            @if(!empty($responsibilities))
                                <div style="font-weight: 600; color: #2c3e50; margin-top: 4px; clear: both;">Key Responsibilities:</div>
                                <ul style="margin: 6px 0; padding-left: 20px; clear: both;">
                                    @foreach($responsibilities as $resp)
                                        <li style="margin-bottom: 3px;">{{ $resp }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="responsibilities-heading">Key Responsibilities:</div>
                                <div class="job-description">{{ $cleanHtml($exp['description']) }}</div>
                            @endif
                        @endif
                    @else
                        <div class="job-description">{{ $cleanHtml($exp) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Education -->
    @if(!empty($education))
        <div class="section">
            <div class="section-title">Education</div>
            @foreach($education as $edu)
                <div class="education-item">
                    @if(is_array($edu))
                        <div class="education-degree">{{ $cleanHtml($edu['degree'] ?? $edu['qualification'] ?? 'Degree') }}</div>
                        @if(!empty($edu['institution']))
                            <div class="education-institution">{{ $cleanHtml($edu['institution']) }}</div>
                        @endif
                        @if(!empty($edu['year']))
                            <div class="education-year">{{ $edu['year'] }}</div>
                        @endif
                    @else
                        <div>{{ $cleanHtml($edu) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Projects -->
    @if(!empty($projects))
        <div class="section">
            <div class="section-title">Projects</div>
            @foreach($projects as $project)
                <div class="project-item">
                    @if(is_array($project))
                        <div class="project-title">{{ $cleanHtml($project['title'] ?? 'Project') }}</div>
                        @if(!empty($project['description']))
                            <div class="project-description">{{ $cleanHtml($project['description']) }}</div>
                        @endif
                    @else
                        <div class="project-description">{{ $cleanHtml($project) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
