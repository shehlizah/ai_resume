<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Preview</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6f8; margin: 0; padding: 24px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 12px 30px rgba(0,0,0,0.08); }
        h1 { margin: 0 0 6px; font-size: 26px; }
        h2 { margin: 0 0 10px; font-size: 18px; color: #444; }
        .subtitle { color: #666; margin-bottom: 18px; }
        .section { margin-top: 22px; }
        .badge { display: inline-block; background: #eef2ff; color: #4338ca; padding: 6px 10px; border-radius: 8px; font-size: 12px; margin: 4px 6px 0 0; }
        ul { padding-left: 18px; margin: 8px 0; }
        li { margin-bottom: 12px; line-height: 1.6; }
        .muted { color: #777; }
        .chip { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f0; padding: 6px 10px; border-radius: 999px; margin: 3px 6px 3px 0; font-size: 12px; }
    </style>
</head>
<body>
@php
    // Normalize resume data (handles stringified JSON or arrays)
    $raw = $resume->data ?? [];
    $data = is_string($raw) ? (json_decode($raw, true) ?: []) : ($raw ?? []);

    // Helper: clean HTML from string
    $cleanHtml = function($value) {
        if (!$value) return '';
        // Strip HTML tags and decode entities
        $cleaned = strip_tags($value);
        $cleaned = html_entity_decode($cleaned);
        // Clean up extra whitespace
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return trim($cleaned);
    };

    // Helper: convert string list to array
    $toArray = function ($value) use ($cleanHtml) {
        if (is_array($value)) return array_values(array_filter(array_map($cleanHtml, $value), fn($v) => $v !== ''));
        if (is_string($value)) {
            // Strip HTML tags first
            $value = $cleanHtml($value);
            return array_values(array_filter(preg_split('/[,;\n]+/', $value), fn($v) => trim($v) !== ''));
        }
        return [];
    };

    $name = $data['name'] ?? 'Candidate';
    $title = $data['title'] ?? $data['headline'] ?? 'Resume';
    $location = $data['location'] ?? $data['city'] ?? null;
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;
    $summary = $cleanHtml($data['summary'] ?? $data['objective'] ?? '');

    $skills = $toArray($data['skills'] ?? []);

    // Handle experience - could be HTML string, array, or simple list
    $rawExperience = $data['experience'] ?? $data['job_title'] ?? [];
    if (is_string($rawExperience)) {
        // Strip HTML and parse as text
        $cleanExp = $cleanHtml($rawExperience);
        // Try to split by common delimiters for multiple entries
        $expParts = preg_split('/\n{2,}/', $cleanExp);
        $experiences = array_filter($expParts, fn($v) => trim($v) !== '');
    } else {
        $experiences = $rawExperience;
    }

    $education = $data['education'] ?? [];
    $projects = $data['projects'] ?? [];
@endphp

<div class="container">
    <header>
        <h1>{{ $name }}</h1>
        <div class="subtitle">{{ $title }}</div>
        @if($location || $email || $phone)
            <div class="muted">
                @if($location)<span>{{ $location }}</span>@endif
                @if($email)<span> • {{ $email }}</span>@endif
                @if($phone)<span> • {{ $phone }}</span>@endif
            </div>
        @endif
        @if(!empty($resume->score))
            <div class="badge"><i class="bx bx-star"></i> Score: {{ $resume->score }}%</div>
        @endif
    </header>

    @if($summary)
        <section class="section">
            <h2>Summary</h2>
            <div class="muted">{{ $summary }}</div>
        </section>
    @endif

    @if(!empty($skills))
        <section class="section">
            <h2>Skills</h2>
            <div>
                @foreach($skills as $skill)
                    @if($skill)
                        <span class="chip"><i class="bx bx-check"></i> {{ $skill }}</span>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @if(!empty($experiences))
        <section class="section">
            <h2>Experience</h2>
            <ul>
                @foreach($experiences as $exp)
                    @if(is_array($exp))
                        <li>
                            <strong>{{ $cleanHtml($exp['title'] ?? $exp['role'] ?? $exp['position'] ?? 'Experience') }}</strong>
                            @if(!empty($exp['company']))<span class="muted"> • {{ $cleanHtml($exp['company']) }}</span>@endif
                            @if(!empty($exp['from']) || !empty($exp['to']))
                                <div class="muted">{{ ($exp['from'] ?? '') }} - {{ ($exp['to'] ?? 'Present') }}</div>
                            @endif
                            @if(!empty($exp['description']))
                                <div class="muted">{{ $cleanHtml($exp['description']) }}</div>
                            @endif
                        </li>
                    @else
                        <li>{{ $cleanHtml($exp) }}</li>
                    @endif
                @endforeach
            </ul>
        </section>
    @endif

    @if(!empty($education))
        <section class="section">
            <h2>Education</h2>
            <ul>
                @foreach($education as $edu)
                    <li>
                        @if(is_array($edu))
                            <strong>{{ $cleanHtml($edu['degree'] ?? $edu['qualification'] ?? 'Education') }}</strong>
                            @if(!empty($edu['institution']))<span class="muted"> • {{ $cleanHtml($edu['institution']) }}</span>@endif
                            @if(!empty($edu['year']))<div class="muted">{{ $edu['year'] }}</div>@endif
                        @else
                            {{ $cleanHtml($edu) }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if(!empty($projects))
        <section class="section">
            <h2>Projects</h2>
            <ul>
                @foreach($projects as $project)
                    <li>
                        @if(is_array($project))
                            <strong>{{ $cleanHtml($project['title'] ?? 'Project') }}</strong>
                            @if(!empty($project['description']))<div class="muted">{{ $cleanHtml($project['description']) }}</div>@endif
                        @else
                            {{ $cleanHtml($project) }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
</body>
</html>
