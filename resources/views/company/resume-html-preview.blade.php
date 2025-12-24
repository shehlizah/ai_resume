<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resume - {{ $resume->data['name'] ?? 'Candidate' }}</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: Calibri, Arial, sans-serif;
    background: #fff;
    color: #333;
    font-size: 11pt;
    line-height: 1.6;
}

.resume-container {
    max-width: 8.5in;
    margin: auto;
    padding: 0.75in;
}

/* HEADER */
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 14px;
    margin-bottom: 22px;
}

.header h1 {
    font-size: 24pt;
    font-weight: bold;
    text-transform: uppercase;
}

.header .title {
    font-size: 12pt;
    font-weight: 600;
    margin-top: 6px;
}

.header .contact {
    font-size: 10pt;
    margin-top: 6px;
}

/* SECTIONS */
.section {
    margin-bottom: 22px;
}

.section-title {
    font-size: 13pt;
    font-weight: bold;
    border-bottom: 1.5px solid #000;
    margin-bottom: 12px;
    text-transform: uppercase;
}

/* SKILLS */
.skill-item {
    font-size: 10pt;
    margin-bottom: 4px;
}
.skill-item::before {
    content: "• ";
}

/* EXPERIENCE */
.experience-item {
    margin-bottom: 20px;
}
.job-title {
    font-size: 11pt;
    font-weight: bold;
}
.company-name {
    font-size: 10pt;
    font-weight: 600;
    margin-top: 2px;
}
.job-date {
    font-size: 9.5pt;
    font-style: italic;
    margin-bottom: 6px;
}
.responsibilities-heading {
    font-size: 10pt;
    font-weight: bold;
    margin-top: 6px;
}
.job-description ul {
    padding-left: 18px;
    margin-top: 4px;
}
.job-description li {
    margin-bottom: 4px;
    font-size: 10pt;
}

/* EDUCATION */
.education-item {
    margin-bottom: 10px;
}

/* PRINT */
@media print {
    @page { margin: 0.5in; }
}
</style>
</head>

<body>

@php
$raw = $resume->data ?? [];
$data = is_string($raw) ? json_decode($raw, true) : $raw;

$clean = fn($v) => trim(preg_replace('/\s+/', ' ', strip_tags($v ?? '')));

/* ---------- SKILLS FIX ---------- */
$skills = [];
if (!empty($data['skills'])) {
    $rawSkills = $clean($data['skills']);

    // Remove heading text
    $rawSkills = preg_replace('/^(technical skills|core competencies|skills)\s*/i', '', $rawSkills);

    // Protect multi-word skills
    $rawSkills = str_replace(
        ['REST APIs', 'Web Development'],
        ['REST_APIs', 'Web_Development'],
        $rawSkills
    );

    foreach (preg_split('/\s+/', $rawSkills) as $skill) {
        $skill = str_replace('_', ' ', trim($skill));
        if ($skill) $skills[] = $skill;
    }
}

/* ---------- BASIC FIELDS ---------- */
$name = $clean($data['name'] ?? 'Candidate');
$title = $clean($data['title'] ?? '');
$email = $clean($data['email'] ?? '');
$phone = $clean($data['phone'] ?? '');
$address = $clean($data['address'] ?? '');
$summary = $clean($data['summary'] ?? '');

/* ---------- EXPERIENCE ---------- */
$jobTitles = $data['job_title'] ?? [];
$companies = $data['company'] ?? [];
$startDates = $data['start_date'] ?? [];
$endDates = $data['end_date'] ?? [];
$responsibilities = $data['responsibilities'] ?? [];

/* ---------- EDUCATION ---------- */
$degrees = $data['degree'] ?? [];
$fields = $data['field_of_study'] ?? [];
$universities = $data['university'] ?? [];
$years = $data['graduation_year'] ?? [];
@endphp

<div class="resume-container">

<!-- HEADER -->
<div class="header">
    <h1>{{ $name }}</h1>
    <div class="title">{{ $title }}</div>
    <div class="contact">
        {{ $email }} | {{ $phone }} | {{ $address }}
    </div>
</div>

<!-- SUMMARY -->
@if($summary)
<div class="section">
    <div class="section-title">Professional Summary</div>
    <p>{{ $summary }}</p>
</div>
@endif

<!-- SKILLS -->
@if($skills)
<div class="section">
    <div class="section-title">Core Competencies</div>
    @foreach($skills as $skill)
        <div class="skill-item">{{ $skill }}</div>
    @endforeach
</div>
@endif

<!-- EXPERIENCE -->
@if(!empty($jobTitles))
<div class="section">
    <div class="section-title">Professional Experience</div>

    @foreach($jobTitles as $i => $job)
    <div class="experience-item">

        <div class="job-title">{{ $clean($job) }}</div>
        <div class="company-name">{{ $clean($companies[$i] ?? '') }}</div>

        <div class="job-date">
            {{ isset($startDates[$i]) ? \Carbon\Carbon::parse($startDates[$i])->format('M Y') : '' }}
            –
            {{ isset($endDates[$i]) ? \Carbon\Carbon::parse($endDates[$i])->format('M Y') : 'Present' }}
        </div>

        @if(!empty($responsibilities))
        <div class="responsibilities-heading">Key Responsibilities</div>
        <div class="job-description">
            <ul>
                @foreach($responsibilities as $task)
                    <li>{{ $clean($task) }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endforeach
</div>
@endif

<!-- EDUCATION -->
@if(!empty($degrees))
<div class="section">
    <div class="section-title">Education</div>

    @foreach($degrees as $i => $degree)
    <div class="education-item">
        <strong>{{ $clean($degree) }}</strong> – {{ $clean($fields[$i] ?? '') }}<br>
        {{ $clean($universities[$i] ?? '') }} ({{ $clean($years[$i] ?? '') }})
    </div>
    @endforeach
</div>
@endif

</div>
</body>
</html>
