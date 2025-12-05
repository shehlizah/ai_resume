<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\UserResume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\ResumeScoreService;

class UserResumeController extends Controller
{
    /**
     * Show all user resumes
     */
    // public function index()
    // {
    //     $resumes = UserResume::where('user_id', Auth::id())
    //         ->with('template')
    //         ->latest()
    //         ->get();

    //     return view('user.resumes.index', compact('resumes'));
    // }

    public function index()
{
    $user = Auth::user();
    $resumes = UserResume::where('user_id', $user->id)
        ->with('template')
        ->paginate(10);

    $hasActivePackage = $user->hasActivePackage();

    return view('user.resumes.index', compact('resumes', 'hasActivePackage'));
}

    /**
     * Show template selection page
     */
    public function chooseTemplate()
    {
        $templates = Template::where('is_active', 1)->get();
        return view('user.resumes.choose', compact('templates'));
    }

    /**
     * Show form to fill resume details
     */
    public function fillForm($template_id)
    {
        $template = Template::findOrFail($template_id);
        return view('user.resumes.fill', compact('template'));
    }

        private function optimizeCssForPdf($css)
    {
        // Remove flexbox
        $css = preg_replace('/display\s*:\s*flex\s*;?/i', 'display: block;', $css);
        $css = preg_replace('/display\s*:\s*inline-flex\s*;?/i', 'display: inline-block;', $css);
        $css = preg_replace('/flex[^:]*:[^;]+;?/i', '', $css);
        $css = preg_replace('/justify-content\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/align-items\s*:[^;]+;?/i', '', $css);

        // Remove grid
        $css = preg_replace('/display\s*:\s*grid\s*;?/i', 'display: block;', $css);
        $css = preg_replace('/grid-[^:]+:[^;]+;?/i', '', $css);

        // Remove transforms
        $css = preg_replace('/transform\s*:[^;]+;?/i', '', $css);

        // Remove viewport units
        $css = preg_replace('/(\d+(?:\.\d+)?)\s*v[hwminax]+/i', '${1}px', $css);

        // Remove calc
        $css = preg_replace('/calc\([^)]+\)/i', 'auto', $css);

        // Remove position: fixed
        $css = preg_replace('/position\s*:\s*fixed\s*;?/i', 'position: relative;', $css);

        return $css;
    }

    /**
     * Generate PDF - CORRECTED VERSION
     */

/**
 * UPDATED generate() METHOD FOR UserResumeController
 *
 * This uses the DomPdfTemplateSanitizer to automatically fix ANY template
 * Place this in your App\Http\Controllers\UserResumeController
 */

public function generate(Request $request)
{
    try {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'experience' => 'nullable|array',
            'experience.*' => 'nullable|string',
            'education' => 'nullable|array',
            'education.*' => 'nullable|string',
            'job_title' => 'nullable|array',
            'job_title.*' => 'nullable|string',
            'company' => 'nullable|array',
            'company.*' => 'nullable|string',
            'start_date' => 'nullable|array',
            'start_date.*' => 'nullable|string',
            'end_date' => 'nullable|array',
            'end_date.*' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'nullable|string',
            'degree' => 'nullable|array',
            'degree.*' => 'nullable|string',
            'field_of_study' => 'nullable|array',
            'field_of_study.*' => 'nullable|string',
            'university' => 'nullable|array',
            'university.*' => 'nullable|string',
            'graduation_year' => 'nullable|array',
            'graduation_year.*' => 'nullable|string',
            'education_details' => 'nullable|array',
            'education_details.*' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $data = $request->except(['_token', 'template_id', 'profile_picture']);

        // Handle profile picture upload
        $photoPath = null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $userId = Auth::id();
            $timestamp = time();
            $extension = $file->getClientOriginalExtension();
            $filename = "profile_{$userId}_{$timestamp}.{$extension}";

            // Store directly in public/uploads/resumes/photos for direct access
            $destinationPath = public_path('uploads/resumes/photos');

            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $photoPath = 'uploads/resumes/photos/' . $filename;
        }        // Build structured content (same as before)
        $data['experience'] = $this->buildExperienceHtml($data);
        $data['education'] = $this->buildEducationHtml($data);
        $data['skills'] = $this->buildSkillsHtml($data);

        // Calculate resume score
        $scoreService = new ResumeScoreService();
        $scoreData = $scoreService->calculateScore($data);

        // Save to database first (no PDF file, will use browser print)
        $resume = UserResume::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'data' => json_encode($data),
            'photo_path' => $photoPath,
            'generated_pdf_path' => null, // No server-side PDF
            'status' => 'completed',
            'score' => $scoreData['score'],
        ]);

        // Redirect to print-preview page (browser print-to-PDF)
        return redirect()->route('user.resumes.print-preview', $resume->id)
            ->with('success', 'Resume ready! Click "Download PDF" to save.');

    } catch (\Exception $e) {
        \Log::error('Resume generation error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return back()->withInput()->with('error', 'Error generating resume: ' . $e->getMessage());
    }
}

/**
 * Extract CSS from HTML <style> tags
 */
private function extractCssFromHtml($html)
{
    $css = '';

    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
        foreach ($matches[1] as $styleBlock) {
            $css .= trim($styleBlock) . "\n";
        }
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
    }

    return [
        'html' => $html,
        'css' => $css
    ];
}

/**
 * Fill template placeholders
 */
private function fillTemplate($html, $css, $data)
{
    $keys = [
        'name', 'title', 'email', 'phone', 'address', 'summary',
        'experience', 'skills', 'education',
        'certifications', 'projects', 'languages', 'interests', 'picture'
    ];

    $rawHtmlKeys = ['experience', 'skills', 'education', 'certifications', 'projects', 'languages', 'interests'];

    foreach ($keys as $key) {
        $placeholder = '{{' . $key . '}}';

        if (strpos($html, $placeholder) === false) {
            continue;
        }

        $value = $data[$key] ?? '';

        // Handle picture placeholder specially
        if ($key === 'picture') {
            if (!empty($value) && (filter_var($value, FILTER_VALIDATE_URL) || strpos($value, 'uploads/') !== false || strpos($value, 'storage/') !== false)) {
                // If value is a valid URL or path, use it as image
                $replaceValue = '<img src="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" alt="Profile Picture" class="profile-picture">';
            } else {
                // Create avatar with initials if no picture
                $userName = $data['name'] ?? 'User';
                $initials = strtoupper(substr($userName, 0, 1));
                if (preg_match('/\s+/', $userName)) {
                    $parts = explode(' ', $userName);
                    $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts)-1], 0, 1));
                }
                $replaceValue = '<div class="profile-picture profile-avatar" style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; font-family: Arial, sans-serif;">' . $initials . '</div>';
            }
        } elseif (in_array($key, $rawHtmlKeys)) {
            $replaceValue = $value;
        } else {
            $replaceValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        $html = str_replace($placeholder, $replaceValue, $html);
    }

    return $html;
}

    public function xgenerate(Request $request)
    {
        try {
            $validated = $request->validate([
                'template_id' => 'required|exists:templates,id',
                'name' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                // Legacy free-text arrays
                'experience' => 'nullable|array',
                'experience.*' => 'nullable|string',
                'education' => 'nullable|array',
                'education.*' => 'nullable|string',
                // Structured fields (preferred)
                'job_title' => 'nullable|array',
                'job_title.*' => 'nullable|string',
                'company' => 'nullable|array',
                'company.*' => 'nullable|string',
                'start_date' => 'nullable|array',
                'start_date.*' => 'nullable|string',
                'end_date' => 'nullable|array',
                'end_date.*' => 'nullable|string',
                'responsibilities' => 'nullable|array',
                'responsibilities.*' => 'nullable|string',
                'degree' => 'nullable|array',
                'degree.*' => 'nullable|string',
                'field_of_study' => 'nullable|array',
                'field_of_study.*' => 'nullable|string',
                'university' => 'nullable|array',
                'university.*' => 'nullable|string',
                'graduation_year' => 'nullable|array',
                'graduation_year.*' => 'nullable|string',
                'education_details' => 'nullable|array',
                'education_details.*' => 'nullable|string',
                'skills' => 'nullable|string',
            ]);

            $template = Template::findOrFail($request->template_id);
            $data = $request->except(['_token', 'template_id']);

            // Build experience HTML from structured fields
            $data['experience'] = $this->buildExperienceHtml($data);

            // Build education HTML from structured fields
            $data['education'] = $this->buildEducationHtml($data);

            // Build skills HTML if needed
            $data['skills'] = $this->buildSkillsHtml($data);

            // Get the ORIGINAL template HTML and CSS (not PDF-optimized version)
            // This preserves the template designer's intent
            $htmlContent = $template->html_content;
            $cssFromDb = $template->css_content ?? '';

            // Extract any <style> tags from the HTML (for templates with embedded CSS)
            $extracted = $this->extractCssFromHtml($htmlContent);
            $htmlContent = $extracted['html'];
            $cssFromHtml = $extracted['css'];

            // Combine CSS: prioritize database CSS, then add CSS from HTML
            $css = $cssFromDb . "\n" . $cssFromHtml;

            $css = $this->optimizeCssForPdf($css);


            // Fill placeholders in the HTML content
            $filledContent = $this->fillTemplate($htmlContent, '', $data);

            // Build a complete HTML document for PDF generation
                $filledHtmlx = "<!DOCTYPE html>
    <html lang=\"en\">
    <head>
        <meta charset=\"UTF-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
        <title>Resume</title>
        <link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Work+Sans:wght@300;400;600&display=swap\" rel=\"stylesheet\">
        <link href=\"https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;600&display=swap\" rel=\"stylesheet\">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            @page { margin: 15mm; size: A4 portrait; }
            {$css}
        </style>
    </head>
    <body>
        {$filledContent}
    </body>
    </html>";


    $filledHtml = "<!DOCTYPE html>
    <html lang=\"en\">
    <head>
        <meta charset=\"UTF-8\">
        <title>Resume</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'DejaVu Sans', Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                font-size: 11pt;
            }
            @page { margin: 12mm; size: A4 portrait; }

            .job-header, .degree-header { display: table; width: 100%; }
            .job-title, .degree-name { display: table-cell; width: 65%; }
            .job-date, .education-date { display: table-cell; width: 35%; text-align: right; }

            {$css}
        </style>
    </head>
    <body>{$filledContent}</body>
    </html>";


            // Generate PDF using DomPDF
           $pdf = Pdf::loadHTML($filledHtml)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,  // ← CHANGED
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'DejaVu Sans',  // ← ADDED
                'dpi' => 96,
                'chroot' => storage_path('app/public'),
            ]);

            // Create directory if needed
            $directory = storage_path('app/public/resumes');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate filename and save
            $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
            $fullPath = $directory . '/' . $fileName;

            File::put($fullPath, $pdf->output());

            // Save to database
            $resume = UserResume::create([
                'user_id' => Auth::id(),
                'template_id' => $template->id,
                'data' => json_encode($data),
                'generated_pdf_path' => 'resumes/' . $fileName,
                'status' => 'completed',
            ]);

            return redirect()->route('user.resumes.success', $resume->id)
                ->with('success', 'Resume generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Resume generation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error generating resume: ' . $e->getMessage());
        }
    }

    /**
     * Build Experience HTML from structured or legacy data
     */
    private function buildExperienceHtml($data)
    {
        // Check if we have structured job data
        if (isset($data['job_title']) && is_array($data['job_title'])) {
            return $this->buildStructuredExperience($data);
        }

        // Fallback to legacy experience array
        if (isset($data['experience']) && is_array($data['experience'])) {
            return $this->buildLegacyExperience($data['experience']);
        }

        return '';
    }

    /**
     * Build experience from structured fields (job_title, company, etc.)
     */
    private function buildStructuredExperience($data)
    {
        $count = count($data['job_title'] ?? []);
        $htmlExperiences = [];

        for ($i = 0; $i < $count; $i++) {
            $title = $data['job_title'][$i] ?? '';
            $company = $data['company'][$i] ?? '';
            $start = $data['start_date'][$i] ?? '';
            $end = $data['end_date'][$i] ?? '';
            $resp = $data['responsibilities'][$i] ?? '';

            // Skip empty entries
            if (empty(trim($title)) && empty(trim($company)) && empty(trim($resp))) {
                continue;
            }

            $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $companyEsc = htmlspecialchars($company, ENT_QUOTES, 'UTF-8');
            $startEsc = htmlspecialchars($start, ENT_QUOTES, 'UTF-8');
            $endEsc = htmlspecialchars($end, ENT_QUOTES, 'UTF-8');

            // Build date range
            $dateRange = '';
            if ($start || $end) {
                $dateRange = trim($startEsc . ($start && $end ? ' - ' : '') . $endEsc);
            }

            // Build responsibilities list
            $respHtml = $this->buildResponsibilitiesList($resp);

            // Build experience block with flexible class names for both templates
            $block = '<div class="experience-item timeline-item">';

            // Job header with title and date
            $block .= '<div class="job-header timeline-header">';
            $block .= '<h3 class="job-title position-title">' . $titleEsc . '</h3>';
            if ($dateRange) {
                $block .= '<span class="job-date date-range">' . $dateRange . '</span>';
            }
            $block .= '</div>';

            // Company name
            if ($companyEsc) {
                $block .= '<div class="company-name organization">' . $companyEsc . '</div>';
            }

            // Responsibilities
            $block .= $respHtml;
            $block .= '</div>';

            $htmlExperiences[] = $block;
        }

        return implode("\n", $htmlExperiences);
    }

    /**
     * Build experience from legacy format (simple text array)
     */
    private function buildLegacyExperience($experiences)
    {
        $experiences = array_filter($experiences);
        if (empty($experiences)) {
            return '';
        }

        $htmlExperiences = [];
        foreach ($experiences as $exp) {
            $escaped = htmlspecialchars($exp, ENT_QUOTES, 'UTF-8');
            $htmlExperiences[] = '<div class="experience-item">' . nl2br($escaped) . '</div>';
        }

        return implode("\n", $htmlExperiences);
    }

    /**
     * Build responsibilities list from text
     */
    private function buildResponsibilitiesList($resp)
    {
        if (empty(trim($resp))) {
            return '';
        }

        $lines = preg_split('/\r?\n/', $resp);
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Remove bullet points or dashes if present
            $line = preg_replace('/^[-•*]\s*/', '', $line);

            if ($line !== '') {
                $items[] = '<li>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
            }
        }

        if (!empty($items)) {
            return '<ul class="job-responsibilities">' . implode('', $items) . '</ul>';
        }

        return '';
    }

    /**
     * Build Education HTML from structured or legacy data
     */
    private function buildEducationHtml($data)
    {
        // Check if we have structured degree data
        if (isset($data['degree']) && is_array($data['degree'])) {
            return $this->buildStructuredEducation($data);
        }

        // Fallback to legacy education array
        if (isset($data['education']) && is_array($data['education'])) {
            return $this->buildLegacyEducation($data['education']);
        }

        return '';
    }

    /**
     * Build education from structured fields
     */
    private function buildStructuredEducation($data)
    {
        $count = count($data['degree'] ?? []);
        $htmlEducations = [];

        for ($i = 0; $i < $count; $i++) {
            $degree = $data['degree'][$i] ?? '';
            $field = $data['field_of_study'][$i] ?? '';
            $univ = $data['university'][$i] ?? '';
            $grad = $data['graduation_year'][$i] ?? '';
            $details = $data['education_details'][$i] ?? '';

            if (empty(trim($degree)) && empty(trim($univ)) && empty(trim($details))) {
                continue;
            }

            $degreeEsc = htmlspecialchars($degree, ENT_QUOTES, 'UTF-8');
            $fieldEsc = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
            $univEsc = htmlspecialchars($univ, ENT_QUOTES, 'UTF-8');
            $gradEsc = htmlspecialchars($grad, ENT_QUOTES, 'UTF-8');

            // Build details section
            $detailsHtml = '';
            if (!empty(trim($details))) {
                $detailsHtml = '<div class="education-details">'
                    . nl2br(htmlspecialchars($details, ENT_QUOTES, 'UTF-8'))
                    . '</div>';
            }

            // Build education block with flexible class names for both templates
            $block = '<div class="education-item education-card">';

            // Degree header
            $block .= '<div class="degree-header">';
            $block .= '<h3 class="degree-name degree-title">' . $degreeEsc . '</h3>';
            if ($gradEsc) {
                $block .= '<span class="education-date edu-date">' . $gradEsc . '</span>';
            }
            $block .= '</div>';

            // Institution
            if ($univEsc) {
                $block .= '<div class="institution-name school-name">' . $univEsc . '</div>';
            }

            // Field of study
            if ($fieldEsc) {
                $block .= '<div class="field-of-study">' . $fieldEsc . '</div>';
            }

            // Additional details
            $block .= $detailsHtml;
            $block .= '</div>';

            $htmlEducations[] = $block;
        }

        return implode("\n", $htmlEducations);
    }

    /**
     * Build education from legacy format
     */
    private function buildLegacyEducation($educations)
    {
        $educations = array_filter($educations);
        if (empty($educations)) {
            return '';
        }

        $htmlEducations = [];
        foreach ($educations as $edu) {
            $escaped = htmlspecialchars($edu, ENT_QUOTES, 'UTF-8');
            $htmlEducations[] = '<div class="education-item">' . nl2br($escaped) . '</div>';
        }

        return implode("\n", $htmlEducations);
    }

    /**
     * Build Skills HTML - supports both Modern Geometric and Editorial Minimal templates
     */
    private function buildSkillsHtml($data)
    {
        if (!isset($data['skills'])) {
            return '';
        }

        $skills = $data['skills'];

        // If it's already HTML (contains HTML tags), return as-is
        if (strpos($skills, '<') !== false && strpos($skills, '>') !== false) {
            return $skills;
        }

        // Parse the skills string into an array
        if (strpos($skills, ',') !== false) {
            // Comma-separated
            $skillsArray = array_map('trim', explode(',', $skills));
        } elseif (strpos($skills, "\n") !== false) {
            // Line-separated
            $skillsArray = array_map('trim', explode("\n", $skills));
        } else {
            // Single skill
            $skillsArray = [trim($skills)];
        }

        // Clean up: remove bullets and empty items
        $cleanedSkills = [];
        foreach ($skillsArray as $skill) {
            $skill = trim($skill);
            $skill = preg_replace('/^[-•*]\s*/', '', $skill);
            if (!empty($skill)) {
                $cleanedSkills[] = $skill;
            }
        }

        if (empty($cleanedSkills)) {
            return '';
        }

        // Generate skill items wrapped for both template types:
        // - Modern Geometric expects: <span class="skill-item">Skill</span>
        // - Editorial Minimal expects: <li>Skill</li> inside <div class="skill-category">
        // We'll generate list items and category structure for Editorial Minimal compatibility

        $html = '<div class="skill-category">
    <h3 class="skill-category-title">Technical Skills</h3>
    <ul class="skill-list">' . "\n";

        foreach ($cleanedSkills as $skill) {
            $escaped = htmlspecialchars($skill, ENT_QUOTES, 'UTF-8');
            $html .= '        <li><span class="skill-item">' . $escaped . '</span></li>' . "\n";
        }

        $html .= '    </ul>
</div>';

        return $html;
    }

    /**
     * Fill HTML template with user data
     */
    /**
     * Extract CSS from HTML (handles <style> tags)
     * Returns array with 'html' and 'css' keys
     */
    private function xextractCssFromHtml($html)
    {
        $css = '';

        // Match all <style> tags
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
            foreach ($matches[1] as $styleBlock) {
                $css .= trim($styleBlock) . "\n";
            }
            // Remove all <style> tags from HTML
            $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        }

        return [
            'html' => $html,
            'css' => $css
        ];
    }

    private function xfillTemplate($html, $css, $data)
    {
        // Define all possible placeholders
        $keys = [
            'name', 'title', 'email', 'phone', 'address', 'summary',
            'experience', 'skills', 'education',
            'certifications', 'projects', 'languages', 'interests'
        ];

        // Keys that contain HTML fragments (should NOT be escaped)
        $rawHtmlKeys = ['experience', 'skills', 'education', 'certifications', 'projects', 'languages', 'interests'];

        foreach ($keys as $key) {
            $placeholder = '{{' . $key . '}}';

            // Skip if placeholder doesn't exist in HTML
            if (strpos($html, $placeholder) === false) {
                continue;
            }

            $value = $data[$key] ?? '';

            if (in_array($key, $rawHtmlKeys)) {
                // Already HTML - use as-is
                $replaceValue = $value;
            } else {
                // Escape plain text fields
                $replaceValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }

            $html = str_replace($placeholder, $replaceValue, $html);
        }

        return $html;
    }

    /**
     * Build PDF HTML - EXACTLY like preview
     */
    private function buildPdfHtml($html, $css)
    {
        // Only replace CSS variables - nothing else
        $css = $this->fixCssForPdf($css);

        // Build document EXACTLY like preview (except @page for PDF)
        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Resume</title>
    <link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Work+Sans:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f5f5f5; padding: 20px; }
        {$css}
    </style>
</head>
<body>
    {$html}
</body>
</html>";
    }

    /**
     * Minimal CSS fixes for PDF - only CSS variables
     */
    private function fixCssForPdf($css)
    {
        // Extract CSS variables
        $variables = [];
        if (preg_match('/:root\s*\{([^}]+)\}/s', $css, $match)) {
            preg_match_all('/--([\w-]+)\s*:\s*([^;]+);/i', $match[1], $varMatches, PREG_SET_ORDER);
            foreach ($varMatches as $varMatch) {
                $variables['--' . $varMatch[1]] = trim($varMatch[2]);
            }
        }

        // Replace var() with actual values
        $css = preg_replace_callback('/var\((--[\w-]+)(?:,\s*([^)]+))?\)/i', function($matches) use ($variables) {
            $varName = $matches[1];
            $fallback = $matches[2] ?? '#333333';
            return $variables[$varName] ?? $fallback;
        }, $css);

        // Remove :root block
        $css = preg_replace('/:root\s*\{[^}]+\}/s', '', $css);

        return $css;
    }

    /**
     * Show print-preview page with user's actual data
     */
    public function printPreview($id)
    {
        $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);
        $template = Template::findOrFail($resume->template_id);
        $user = Auth::user();

        // Get user's data from database
        $userData = json_decode($resume->data, true);

        // Add profile picture URL if exists
        if ($resume->photo_path) {
            // Use asset() for direct public path access
            $userData['picture'] = asset($resume->photo_path);
        } else {
            $userData['picture'] = ''; // Empty if no picture
        }

        // Get user's subscription package type for score feedback
        $activeSubscription = $user->activeSubscription()->with('plan')->first();
        $packageType = $activeSubscription && $activeSubscription->plan
            ? strtolower($activeSubscription->plan->slug ?? 'basic')
            : 'basic';

        // Calculate score and get package-based feedback
        $scoreService = new ResumeScoreService();
        $scoreData = $scoreService->calculateScore($userData);
        $feedback = $scoreService->getPackageBasedFeedback($packageType, $scoreData);

        // Get template content
        $htmlContent = $template->html_content;
        $cssFromDb = $template->css_content ?? '';

        // Extract any <style> tags from the HTML
        $extracted = $this->extractCssFromHtml($htmlContent);
        $htmlContent = $extracted['html'];
        $cssFromHtml = $extracted['css'];

        // Combine CSS
        $css = $cssFromDb . "\n" . $cssFromHtml;

        // Replace CSS variables
        $css = $this->fixCssForPdf($css);

        // Fill placeholders with user data
        $filledContent = $this->fillTemplate($htmlContent, '', $userData);

        // Build score badge HTML - For header display
        $scoreColor = $feedback['score'] >= 80 ? '#10b981' : ($feedback['score'] >= 60 ? '#f59e0b' : '#ef4444');
        
        // Create header score badge
        $headerScoreBadge = "
        <div class=\"header-score\">
            <div class=\"header-score-number\" style=\"color: white;\">
                {$feedback['score']}
            </div>
            <div class=\"header-score-info\">
                <div class=\"header-score-grade\" style=\"background: {$scoreColor};\">
                    {$feedback['grade']}
                </div>
                <div class=\"header-score-label\">Resume Score</div>
            </div>
        </div>";
        
        // Old score badge (now hidden via CSS)
        $scoreBadge = "";

        // Check if user has active package for download button
        $hasActivePackage = $user->activeSubscription()->exists();

        // Build download button HTML (only if user has active package)
        $downloadButton = '';
        $printBlockScript = '';

        if ($hasActivePackage) {
            $downloadButton = '
    <a href="#" onclick="window.print(); return false;" class="download-btn no-print">
        📥 Download PDF
    </a>';
        } else {
            $downloadButton = '
    <a href="' . route('user.pricing') . '" class="download-btn no-print" style="background: #f59e0b;">
        🔒 Upgrade to Download
    </a>';

            // Block all print methods for non-subscribed users
            $printBlockScript = "
    <script>
        // Disable Ctrl+P, Cmd+P, and right-click print
        document.addEventListener('keydown', function(e) {
            // Block Ctrl+P / Cmd+P
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                e.stopPropagation();
                alert('⚠️ Print/Download is only available for subscribed users. Please upgrade your plan to access this feature.');
                return false;
            }
        }, true);

        // Block window.print() function
        window.print = function() {
            alert('⚠️ Print/Download is only available for subscribed users. Please upgrade your plan to access this feature.');
            window.location.href = '" . route('user.pricing') . "';
        };

        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Block beforeprint event
        window.addEventListener('beforeprint', function(e) {
            e.preventDefault();
            e.stopPropagation();
            alert('⚠️ Print/Download is only available for subscribed users. Please upgrade your plan to access this feature.');
            window.location.href = '" . route('user.pricing') . "';
        }, true);

        // Additional protection - monitor print media query
        const printMediaQuery = window.matchMedia('print');
        printMediaQuery.addListener(function(mql) {
            if (mql.matches) {
                alert('⚠️ Print/Download is only available for subscribed users. Please upgrade your plan to access this feature.');
                window.location.href = '" . route('user.pricing') . "';
            }
        });
    </script>";
        }

        // Build HTML document with header, footer, and navigation
        $output = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Resume Preview</title>
    <link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Work+Sans:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <link href=\"https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css\" rel=\"stylesheet\">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: #e5e7eb;
            padding-top: 180px;
            padding-bottom: 80px;
        }

        /* Print styles - Remove browser headers/footers */
        @media print {
            body { background: white; padding: 0; padding-top: 0; padding-bottom: 0; }
            .no-print { display: none !important; }
            .a4-wrapper { box-shadow: none; margin: 0; }

            /* Remove default browser header/footer */
            @page {
                margin: 0;
                size: A4;
            }
        }

        /* Header */
        .preview-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 9998;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .header-content {
            flex: 1;
        }

        .preview-header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .preview-header p {
            font-size: 13px;
            opacity: 0.9;
        }

        /* Score Badge in Header */
        .header-score {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .header-score-number {
            font-size: 36px;
            font-weight: bold;
            line-height: 1;
        }

        .header-score-info {
            text-align: left;
        }

        .header-score-grade {
            padding: 4px 12px;
            border-radius: 6px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 4px;
        }

        .header-score-label {
            font-size: 11px;
            opacity: 0.9;
        }

        /* Action Cards Container */
        .action-cards {
            position: fixed;
            top: 100px;
            left: 0;
            right: 0;
            display: flex;
            gap: 10px;
            padding: 10px 20px;
            background: #f5f5f5;
            z-index: 9997;
            border-bottom: 1px solid #e5e7eb;
        }

        .action-card {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
        }

        .action-card.primary { border-top: 3px solid #667eea; }
        .action-card.success { border-top: 3px solid #10b981; }
        .action-card.warning { border-top: 3px solid #f59e0b; }

        .action-card i {
            font-size: 24px;
            margin-bottom: 6px;
        }

        .action-card.primary i { color: #667eea; }
        .action-card.success i { color: #10b981; }
        .action-card.warning i { color: #f59e0b; }

        .action-card strong {
            font-size: 13px;
            display: block;
            margin-bottom: 3px;
        }

        .action-card small {
            font-size: 10px;
            color: #666;
        }

        /* Score Badge - Hidden (moved to header) */
        .score-badge {
            display: none;
        }

        /* A4 Paper View */
        .a4-wrapper {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }

        .resume-container {
            padding: 0;
        }

        /* Footer */
        .preview-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 12px 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            z-index: 9998;
        }

        /* Download button */
        .download-btn {
            position: fixed;
            bottom: 50px;
            right: 20px;
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .download-btn:hover {
            background: #5568d3;
            transform: scale(1.05);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 210px;
                background: #f5f5f5;
            }

            .preview-header {
                padding: 12px 15px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .header-content h1 {
                font-size: 18px;
            }

            .header-content p {
                font-size: 11px;
            }

            .header-score {
                width: 100%;
                justify-content: space-between;
                padding: 8px 15px;
            }

            .header-score-number {
                font-size: 28px;
            }

            .header-score-grade {
                font-size: 10px;
                padding: 3px 10px;
            }

            .header-score-label {
                font-size: 10px;
            }

            .action-cards {
                top: 145px;
                padding: 10px;
                gap: 10px;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                justify-content: center;
            }

            .action-card {
                min-width: 60px;
                max-width: 60px;
                padding: 12px 8px;
                flex-shrink: 0;
            }

            .action-card i {
                font-size: 28px;
                margin-bottom: 0;
            }

            .action-card div {
                display: none;
            }

            .action-card strong,
            .action-card small {
                display: none;
            }

            .a4-wrapper {
                width: 100%;
                min-height: auto;
                margin: 10px auto;
                box-shadow: none;
            }

            .download-btn {
                bottom: 45px;
                right: 10px;
                left: 10px;
                width: calc(100% - 20px);
                justify-content: center;
                padding: 12px 20px;
                font-size: 13px;
            }

            .preview-footer {
                padding: 8px 10px;
                font-size: 10px;
            }
        }

        {$css}
    </style>
    {$printBlockScript}
</head>
<body>
    <!-- Header with Score -->
    <div class=\"preview-header no-print\">
        <div class=\"header-content\">
            <h1>📄 Resume Preview</h1>
            <p>Review your resume and take the next step in your career journey</p>
        </div>
        {$headerScoreBadge}
    </div>

    <!-- Action Cards -->
    <div class=\"action-cards no-print\">
        <a href=\"" . route('user.resumes.index') . "\" class=\"action-card primary\">
            <i class='bx bx-arrow-back'></i>
            <div>
                <strong>Go Back</strong>
                <small>Return to My Resumes</small>
            </div>
        </a>
        <a href=\"" . route('user.interview.prep') . "\" class=\"action-card success\">
            <i class='bx bx-microphone'></i>
            <div>
                <strong>Interview Prep</strong>
                <small>Practice with AI</small>
            </div>
        </a>
        <a href=\"" . route('user.jobs.by-location') . "\" class=\"action-card warning\">
            <i class='bx bx-map'></i>
            <div>
                <strong>Job by Location</strong>
                <small>Find nearby jobs</small>
            </div>
        </a>
    </div>

    {$downloadButton}
    {$scoreBadge}

    <!-- Resume Content in A4 Paper View -->
    <div class=\"a4-wrapper\">
        <div class=\"resume-container\">
            {$filledContent}
        </div>
    </div>

    <!-- Footer -->
    <div class=\"preview-footer no-print\">
        <p>© 2025 AI Resume Builder. Need help? <a href=\"#\" style=\"color: #667eea; text-decoration: none;\">Contact Support</a></p>
    </div>
</body>
</html>";

        return response($output)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Preview template with sample data
     */
    public function preview($template_id)
    {
        $template = Template::findOrFail($template_id);

        // Use the original HTML and CSS, not the PDF-optimized version
        $htmlContent = $template->html_content;
        $cssFromDb = $template->css_content ?? '';

        // Extract any <style> tags from the HTML (for templates with embedded CSS)
        $extracted = $this->extractCssFromHtml($htmlContent);
        $htmlContent = $extracted['html'];
        $cssFromHtml = $extracted['css'];

        // Combine CSS: prioritize database CSS, then add CSS from HTML
        $css = $cssFromDb . "\n" . $cssFromHtml;

        // Sample data for preview
        $sampleData = $this->getSampleData();

        // Fill placeholders in the HTML content
        $filledContent = $this->fillTemplate($htmlContent, '', $sampleData);

        // Build a complete HTML document
        $output = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Resume Preview</title>
    <link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Work+Sans:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;600&display=swap\" rel=\"stylesheet\">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f5f5f5; padding: 20px; }
        {$css}
    </style>
</head>
<body>
    {$filledContent}
</body>
</html>";

        return response($output)->header('Content-Type', 'text/html; charset=UTF-8');
    }

/**
 * Get sample data for preview - FIXED SYNTAX
 */
private function getSampleData()
{
    return [
        'name' => 'John Doe',
        'title' => 'Senior Software Engineer',
        'email' => 'john.doe@example.com',
        'phone' => '+1 (555) 123-4567',
        'address' => 'San Francisco, CA',
        'picture' => '', // No picture in sample data
        'summary' => 'Experienced software engineer with 10+ years of expertise in full-stack development, cloud architecture, and agile methodologies. Proven track record of delivering high-quality solutions and leading cross-functional teams to success.',
        'experience' => '<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Senior Software Engineer</h3>
        <span class="job-date">Jan 2020 - Present</span>
    </div>
    <div class="company-name">TechCorp Inc.</div>
    <ul class="job-responsibilities">
        <li>Led development of microservices architecture serving 1M+ users with 99.9% uptime</li>
        <li>Mentored team of 5 junior developers, conducting code reviews and technical training</li>
        <li>Improved system performance by 40% through database optimization and caching strategies</li>
    </ul>
</div>
<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Software Developer</h3>
        <span class="job-date">Jun 2018 - Dec 2019</span>
    </div>
    <div class="company-name">StartUp LLC</div>
    <ul class="job-responsibilities">
        <li>Developed RESTful APIs using Laravel and Node.js</li>
        <li>Implemented automated testing suite reducing production bugs by 60%</li>
    </ul>
</div>',
        'education' => '<div class="education-item">
    <div class="degree-header">
        <h3 class="degree-name">Bachelor of Science in Computer Science</h3>
        <span class="education-date">2014 - 2018</span>
    </div>
    <div class="institution-name">University of California, Berkeley</div>
</div>',
        'skills' => '<div class="skill-category">
    <h3 class="skill-category-title">Technical Skills</h3>
    <ul class="skill-list">
        <li>PHP</li>
        <li>Laravel</li>
        <li>JavaScript</li>
        <li>React</li>
        <li>MySQL</li>
        <li>Docker</li>
        <li>AWS</li>
        <li>Git</li>
    </ul>
</div>
<div class="skill-category">
    <h3 class="skill-category-title">Professional Skills</h3>
    <ul class="skill-list">
        <li>Full Stack Development</li>
        <li>System Architecture</li>
        <li>Team Leadership</li>
        <li>Agile Methodology</li>
        <li>Cloud Computing</li>
    </ul>
</div>',
    ];
}

    /**
     * View PDF in browser
     */
    public function view($id)
    {
        // Redirect to print-preview since we use browser print-to-PDF
        return redirect()->route('user.resumes.print-preview', $id);
    }

    /**
     * Download PDF - Redirects to print preview where user can use browser print
     */
    public function download($id)
    {
        // Redirect to dashboard after download
        return redirect()->route('user.dashboard')
            ->with('success', 'Resume ready for download!');
    }

    /**
     * Delete a resume
     */
    public function destroy($id)
    {
        $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

        // Delete file
        $filePath = storage_path('app/public/' . $resume->generated_pdf_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $resume->delete();

        return redirect()->route('user.resumes.index')
            ->with('success', 'Resume deleted successfully!');
    }

    /**
     * Show success page
     */
    // public function success($id)
    // {
    //     $resume = UserResume::where('id', $id)
    //         ->where('user_id', auth()->id())
    //         ->with('template')
    //         ->firstOrFail();

    //     return view('user.resumes.success', compact('resume'));
    // }

    public function success($id)
{
    $resume = UserResume::where('id', $id)
        ->where('user_id', auth()->id())
        ->with('template')
        ->firstOrFail();

    return view('user.resumes.success', compact('resume'));
}


    // AI generation methods remain the same...
    // (I'll skip them to keep this focused, but they should stay as-is)


    // ADD THESE METHODS TO YOUR UserResumeController.php
// Place them at the end of the class, before the closing }

/**
 * Generate Experience Content with AI
 */
public function generateExperienceAI(Request $request)
{
    try {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'company' => 'required|string',
            'years' => 'required|numeric|min:0',
            'responsibilities' => 'nullable|string',
        ]);

        $prompt = "Generate a professional resume experience entry for someone who worked as a {$validated['job_title']} at {$validated['company']} for {$validated['years']} years";

        if (!empty($validated['responsibilities'])) {
            $prompt .= ". Key responsibilities: {$validated['responsibilities']}";
        }

        $prompt .= ". Format as bullet points with 3-4 achievement statements. Make it professional and impactful. Return ONLY the bullet points, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Experience Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate Skills Content with AI
 */
public function generateSkillsAI(Request $request)
{
    try {
        $validated = $request->validate([
            'role' => 'required|string',
            'level' => 'required|in:junior,mid,senior',
            'fields' => 'nullable|string',
        ]);

        $levelDescriptions = [
            'junior' => 'junior-level (0-3 years experience)',
            'mid' => 'mid-level (3-7 years experience)',
            'senior' => 'senior-level (7+ years experience)',
        ];

        $prompt = "Generate a comprehensive skills list for a {$levelDescriptions[$validated['level']]} {$validated['role']}";

        if (!empty($validated['fields'])) {
            $prompt .= " with expertise in: {$validated['fields']}";
        }

        $prompt .= ". Include technical skills, programming languages, frameworks, tools, and soft skills. Format as a comma-separated list. Make it professional and industry-relevant. Return ONLY the skills list, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Skills Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate Education Content with AI
 */
public function generateEducationAI(Request $request)
{
    try {
        $validated = $request->validate([
            'degree' => 'required|string',
            'field_of_study' => 'required|string',
            'university' => 'required|string',
            'graduation_year' => 'required|numeric|min:1950|max:2030',
        ]);

        $prompt = "Generate professional education details for someone with a {$validated['degree']} in {$validated['field_of_study']} from {$validated['university']}, graduated in {$validated['graduation_year']}";

        $prompt .= ". Include relevant coursework, honors, GPA (if applicable), and achievements. Keep it concise (2-3 lines) and professional. Return ONLY the education details, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Education Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate Professional Summary with AI
 */
public function generateSummaryAI(Request $request)
{
    try {
        $validated = $request->validate([
            'role' => 'required|string',
            'years' => 'required|numeric|min:0',
            'skills' => 'nullable|string',
            'goal' => 'nullable|string',
        ]);

        $prompt = "Generate a compelling 2-3 sentence professional summary for a {$validated['role']} with {$validated['years']} years of experience";

        if (!empty($validated['skills'])) {
            $prompt .= ". Key skills: {$validated['skills']}";
        }

        if (!empty($validated['goal'])) {
            $prompt .= ". Career goal: {$validated['goal']}";
        }

        $prompt .= ". Make it professional, engaging, and suitable for a resume. It should highlight achievements and value proposition. Return ONLY the summary text, no introductory phrases.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Summary Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Call OpenAI API - WITH PROPER ERROR HANDLING
 */
private function callOpenAI($prompt)
{
    try {
        $apiKey = config('services.openai.api_key');

        // Check if API key is configured
        if (empty($apiKey)) {
            \Log::error('OpenAI API key not configured');
            throw new \Exception('OpenAI API key is not configured. Please add it to your .env file.');
        }

        // Validate API key format
        if (!str_starts_with($apiKey, 'sk-')) {
            \Log::error('Invalid OpenAI API key format');
            throw new \Exception('Invalid OpenAI API key format. Key should start with "sk-".');
        }

        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional resume writer. Generate clear, concise, and impactful resume content. Always provide only the requested content without any introductory phrases or explanations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ],
            'http_errors' => false,
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        // Check if response is HTML (error page)
        if (str_starts_with(trim($body), '<!DOCTYPE') || str_starts_with(trim($body), '<html')) {
            \Log::error('OpenAI API returned HTML instead of JSON', [
                'status_code' => $statusCode,
                'body_preview' => substr($body, 0, 200)
            ]);
            throw new \Exception('API returned an error page. This usually means authentication failed or the API is unavailable.');
        }

        // Try to decode JSON
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Failed to decode OpenAI API response', [
                'json_error' => json_last_error_msg(),
                'status_code' => $statusCode,
                'body_preview' => substr($body, 0, 200)
            ]);
            throw new \Exception('Invalid response from OpenAI API: ' . json_last_error_msg());
        }

        // Check for API errors
        if ($statusCode !== 200) {
            $errorMessage = $data['error']['message'] ?? 'Unknown error';
            $errorType = $data['error']['type'] ?? 'unknown';

            \Log::error('OpenAI API error', [
                'status_code' => $statusCode,
                'error_type' => $errorType,
                'error_message' => $errorMessage
            ]);

            if ($statusCode === 401) {
                throw new \Exception('Invalid OpenAI API key. Please check your configuration.');
            } elseif ($statusCode === 429) {
                throw new \Exception('OpenAI API rate limit exceeded. Please try again later.');
            } elseif ($statusCode === 500) {
                throw new \Exception('OpenAI API is currently unavailable. Please try again later.');
            } else {
                throw new \Exception('OpenAI API error: ' . $errorMessage);
            }
        }

        // Extract content from response
        if (isset($data['choices'][0]['message']['content'])) {
            $content = trim($data['choices'][0]['message']['content']);

            // Remove common AI prefixes
            $content = preg_replace('/^(Here are|Here is|Sure,?|Certainly,?|Of course,?).*/i', '', $content);
            $content = trim($content);

            return $content;
        }

        \Log::error('Unexpected OpenAI API response structure', ['data' => $data]);
        throw new \Exception('Unexpected response structure from OpenAI API.');

    } catch (\GuzzleHttp\Exception\ConnectException $e) {
        \Log::error('Failed to connect to OpenAI API', ['error' => $e->getMessage()]);
        throw new \Exception('Could not connect to OpenAI API. Please check your internet connection.');
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        \Log::error('OpenAI API request failed', ['error' => $e->getMessage()]);
        throw new \Exception('Request to OpenAI API failed: ' . $e->getMessage());
    } catch (\Exception $e) {
        // Re-throw our custom exceptions
        if (strpos($e->getMessage(), 'OpenAI') !== false) {
            throw $e;
        }

        \Log::error('Unexpected error in callOpenAI', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw new \Exception('An unexpected error occurred while generating content.');
    }
}

    /**
     * Upload temporary resume file for job finder and interview prep
     */
    public function uploadTemporary(Request $request)
    {
        \Log::info('Upload temp resume called');

        try {
            // Validate the request
            $validated = $request->validate([
                'resume_file' => 'required|file|mimes:pdf,doc,docx|max:10240' // 10MB
            ], [
                'resume_file.required' => 'Please select a file to upload',
                'resume_file.file' => 'Please upload a valid file',
                'resume_file.mimes' => 'Only PDF and DOCX files are allowed',
                'resume_file.max' => 'File size must be less than 10MB'
            ]);

            $file = $request->file('resume_file');
            $user = Auth::user();

            \Log::info('File upload details', [
                'user_id' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType()
            ]);

            // Generate unique filename
            $timestamp = now()->timestamp;
            $randomStr = \Illuminate\Support\Str::random(8);
            $extension = $file->getClientOriginalExtension();
            $filename = "resume_{$timestamp}_{$randomStr}.{$extension}";

            // Create directory path for temp uploads - use full absolute path
            $uploadDir = "uploads/temp/{$user->id}";

            // Use the full storage path directly to ensure file is stored in the right place
            $fullPath = storage_path("app/private/{$uploadDir}");
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                \Log::info('Created upload directory', ['path' => $fullPath]);
            }

            // Store file directly to absolute path
            $filePath = $fullPath . DIRECTORY_SEPARATOR . $filename;
            if (!$file->move($fullPath, $filename)) {
                throw new \Exception('Failed to move uploaded file');
            }

            \Log::info('File stored successfully', [
                'absolute_path' => $filePath,
                'relative_path' => $uploadDir . DIRECTORY_SEPARATOR . $filename,
                'exists' => file_exists($filePath),
                'size' => filesize($filePath)
            ]);

            return response()->json([
                'success' => true,
                'file_path' => $uploadDir . '/' . $filename,  // Return relative path for backend resolution
                'file_name' => $file->getClientOriginalName(),
                'message' => 'Resume uploaded successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Resume upload validation failed', ['errors' => $e->errors()]);

            $errorMsg = 'Validation failed';
            if (isset($e->errors()['resume_file'])) {
                $errorMsg = $e->errors()['resume_file'][0];
            }

            return response()->json([
                'success' => false,
                'message' => $errorMsg
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Resume upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
