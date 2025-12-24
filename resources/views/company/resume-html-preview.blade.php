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
        li { margin-bottom: 6px; }
        .muted { color: #777; }
        .chip { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f0; padding: 6px 10px; border-radius: 999px; margin: 3px 6px 3px 0; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>{{ $resume->data['name'] ?? 'Candidate' }}</h1>
        <div class="subtitle">{{ $resume->data['title'] ?? $resume->data['headline'] ?? 'Resume' }}</div>
        @if(!empty($resume->score))
            <div class="badge"><i class="bx bx-star"></i> Score: {{ $resume->score }}%</div>
        @endif
    </header>

    @if(!empty($resume->data['summary']))
        <section class="section">
            <h2>Summary</h2>
            <div class="muted">{{ $resume->data['summary'] }}</div>
        </section>
    @endif

    @if(!empty($resume->data['skills']) && is_array($resume->data['skills']))
        <section class="section">
            <h2>Skills</h2>
            <div>
                @foreach($resume->data['skills'] as $skill)
                    <span class="chip"><i class="bx bx-check"></i> {{ $skill }}</span>
                @endforeach
            </div>
        </section>
    @endif

    @if(!empty($resume->data['job_title']) && is_array($resume->data['job_title']))
        <section class="section">
            <h2>Experience</h2>
            <ul>
                @foreach($resume->data['job_title'] as $jobTitle)
                    <li>{{ $jobTitle }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if(!empty($resume->data['education']) && is_array($resume->data['education']))
        <section class="section">
            <h2>Education</h2>
            <ul>
                @foreach($resume->data['education'] as $edu)
                    <li>{{ is_array($edu) ? ($edu['degree'] ?? $edu['institution'] ?? json_encode($edu)) : $edu }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if(!empty($resume->data['projects']) && is_array($resume->data['projects']))
        <section class="section">
            <h2>Projects</h2>
            <ul>
                @foreach($resume->data['projects'] as $project)
                    <li>{{ is_array($project) ? ($project['title'] ?? json_encode($project)) : $project }}</li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
</body>
</html>
