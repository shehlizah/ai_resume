<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\UserResume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserResumeController extends Controller
{
    /**
     * Show all user resumes
     */
    public function index()
    {
        $resumes = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->latest()
            ->get();

        return view('user.resumes.index', compact('resumes'));
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

    /**
     * Generate PDF from HTML template and user data
     * Saves to database, then redirects to success page that opens PDF
     */
    public function old_generate(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'template_id' => 'required|exists:templates,id',
                'name' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string',
                'experience' => 'nullable|string',
                'skills' => 'nullable|string',
                'education' => 'nullable|string',
            ]);

            // Get template
            $template = Template::findOrFail($request->template_id);

            // Prepare user data
            $data = $request->except(['_token', 'template_id']);

            // Read HTML template from public/templates/html/
            $htmlPath = public_path("templates/html/{$template->slug}.html");

            if (!File::exists($htmlPath)) {
                return back()->with('error', 'Template HTML file not found at: templates/html/' . $template->slug . '.html');
            }

            $html = File::get($htmlPath);

            // Read CSS if exists
            $cssPath = public_path("templates/css/{$template->slug}.css");
            $css = '';

            if (File::exists($cssPath)) {
                $css = File::get($cssPath);
            }

            // Replace placeholders with actual user data
            $filledHtml = $this->fillTemplate($html, $css, $data);

            // Generate PDF
            $pdf = Pdf::loadHTML($filledHtml)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            // Generate unique filename
            $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
            $filePath = 'resumes/' . $fileName;

            // Save PDF to storage
            Storage::put('public/' . $filePath, $pdf->output());

            // Save resume record to database
            $resume = UserResume::create([
                'user_id' => Auth::id(),
                'template_id' => $template->id,
                'data' => json_encode($data),
                'generated_pdf_path' => $filePath,
                'status' => 'completed',
            ]);

            // Redirect to success page with resume ID
            return redirect()->route('user.resumes.success', $resume->id)
                ->with('success', 'Resume generated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error generating resume: ' . $e->getMessage());
        }
    }

    public function xgenerate(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $data = $request->except(['_token', 'template_id']);

        // UPDATED: Read from storage/app/public/templates/
        $htmlPath = storage_path("app/public/templates/html/{$template->slug}.html");

        if (!File::exists($htmlPath)) {
            return back()->with('error', 'Template HTML file not found at: ' . $htmlPath);
        }

        $html = File::get($htmlPath);

        // UPDATED: Read CSS from storage
        $cssPath = storage_path("app/public/templates/css/{$template->slug}.css");
        $css = '';

        if (File::exists($cssPath)) {
            $css = File::get($cssPath);
        }

        // Replace placeholders
        $filledHtml = $this->fillTemplate($html, $css, $data);

        // Generate PDF
        $pdf = Pdf::loadHTML($filledHtml)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Save PDF
        $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
        $filePath = 'resumes/' . $fileName;

        Storage::put('public/' . $filePath, $pdf->output());

        // Save to database
        $resume = UserResume::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'data' => json_encode($data),
            'generated_pdf_path' => $filePath,
            'status' => 'completed',
        ]);

        return redirect()->route('user.resumes.success', $resume->id);

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

    /**
     * Success page after resume generation
     * Auto-opens PDF in new tab
     */
    public function old_success($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->findOrFail($id);

        return view('user.resumes.success', compact('resume'));
    }

    /**
     * Fill HTML template with user data
     */
    private function fillTemplate($html, $css, $data)
    {
        // If CSS exists, inject it into HTML
        if (!empty($css)) {
            // Check if HTML already has a <style> tag
            if (strpos($html, '</head>') !== false) {
                $cssTag = "<style>{$css}</style>";
                $html = str_replace('</head>', $cssTag . '</head>', $html);
            } else {
                // Add style tag at the beginning
                $html = "<style>{$css}</style>" . $html;
            }
        }

        // Replace all placeholders with user data
        $placeholders = [
            '{{name}}' => $data['name'] ?? '',
            '{{title}}' => $data['title'] ?? '',
            '{{email}}' => $data['email'] ?? '',
            '{{phone}}' => $data['phone'] ?? '',
            '{{address}}' => $data['address'] ?? '',
            '{{summary}}' => $data['summary'] ?? 'No summary provided',
            '{{experience}}' => $data['experience'] ?? 'No experience provided',
            '{{skills}}' => $data['skills'] ?? 'No skills provided',
            '{{education}}' => $data['education'] ?? 'No education provided',
        ];

        // Replace each placeholder
        foreach ($placeholders as $placeholder => $value) {
            // Escape HTML special characters for security
            $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $html = str_replace($placeholder, $escapedValue, $html);
        }

        return $html;
    }

    /**
     * Preview template with sample data
     */

     public function preview($template_id)
{
    $template = Template::findOrFail($template_id);

    // UPDATED: Read from storage
    $htmlPath = storage_path("app/public/templates/html/{$template->slug}.html");

    if (!File::exists($htmlPath)) {
        return back()->with('error', 'Template HTML file not found!');
    }

    $html = File::get($htmlPath);

    // UPDATED: Read CSS from storage
    $cssPath = storage_path("app/public/templates/css/{$template->slug}.css");
    $css = '';

    if (File::exists($cssPath)) {
        $css = File::get($cssPath);
    }


      // Sample data for preview
        $sampleData = [
            'name' => 'John Doe',
            'title' => 'Software Engineer',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'experience' => "Senior Software Engineer at Tech Corp (2020-Present)\n- Led development of microservices architecture\n- Managed team of 5 developers\n\nSoftware Developer at StartupXYZ (2018-2020)\n- Built RESTful APIs using Laravel\n- Improved application performance by 40%",
            'skills' => "• PHP, Laravel, Vue.js\n• MySQL, PostgreSQL, Redis\n• Docker, AWS, CI/CD\n• RESTful API Design\n• Team Leadership",
            'education' => "Bachelor of Science in Computer Science\nUniversity of Technology (2014-2018)\nGPA: 3.8/4.0\n\nRelevant Coursework:\n- Data Structures & Algorithms\n- Database Systems\n- Web Development",
        ];

        // Fill with sample data
        $filledHtml = $this->fillTemplate($html, $css, $sampleData);

        // Return as HTML for preview
        return response($filledHtml)->header('Content-Type', 'text/html');
}

    public function old_preview($template_id)
    {
        $template = Template::findOrFail($template_id);

        // Read HTML template
        $htmlPath = public_path("templates/html/{$template->slug}.html");

        if (!File::exists($htmlPath)) {
            return back()->with('error', 'Template HTML file not found!');
        }

        $html = File::get($htmlPath);

        // Read CSS if exists
        $cssPath = public_path("templates/css/{$template->slug}.css");
        $css = '';

        if (File::exists($cssPath)) {
            $css = File::get($cssPath);
        }

        // Sample data for preview
        $sampleData = [
            'name' => 'John Doe',
            'title' => 'Software Engineer',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'experience' => "Senior Software Engineer at Tech Corp (2020-Present)\n- Led development of microservices architecture\n- Managed team of 5 developers\n\nSoftware Developer at StartupXYZ (2018-2020)\n- Built RESTful APIs using Laravel\n- Improved application performance by 40%",
            'skills' => "• PHP, Laravel, Vue.js\n• MySQL, PostgreSQL, Redis\n• Docker, AWS, CI/CD\n• RESTful API Design\n• Team Leadership",
            'education' => "Bachelor of Science in Computer Science\nUniversity of Technology (2014-2018)\nGPA: 3.8/4.0\n\nRelevant Coursework:\n- Data Structures & Algorithms\n- Database Systems\n- Web Development",
        ];

        // Fill with sample data
        $filledHtml = $this->fillTemplate($html, $css, $sampleData);

        // Return as HTML for preview
        return response($filledHtml)->header('Content-Type', 'text/html');
    }

    /**
     * Download a specific resume
     */
    public function xdownload($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->findOrFail($id);

        $filePath = 'public/' . $resume->generated_pdf_path;

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        return Storage::download($filePath, 'resume_' . $resume->id . '.pdf');
    }

    /**
     * View/Preview a resume PDF in browser
     */
    public function xview($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->findOrFail($id);

        $filePath = 'public/' . $resume->generated_pdf_path;

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        // Stream PDF to browser
        return response()->file(storage_path('app/' . $filePath));
    }

    /**
     * Delete a resume
     */
    public function destroy($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->findOrFail($id);

        // Delete file from storage
        $filePath = 'public/' . $resume->generated_pdf_path;
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete database record
        $resume->delete();

        return redirect()->route('user.resumes.index')
            ->with('success', 'Resume deleted successfully!');
    }

    /**
 * Generate PDF - FIXED VERSION
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
            'experience' => 'nullable|array',
            'experience.*' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|array',
            'education.*' => 'nullable|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $data = $request->except(['_token', 'template_id']);

        // Merge experience array into single string
        if (isset($data['experience']) && is_array($data['experience'])) {
            $data['experience'] = implode("\n\n", array_filter($data['experience']));
        }

        // Merge education array into single string
        if (isset($data['education']) && is_array($data['education'])) {
            $data['education'] = implode("\n\n", array_filter($data['education']));
        }

        // Read HTML template
        $htmlPath = storage_path("app/public/templates/html/{$template->slug}.html");
        if (!File::exists($htmlPath)) {
            return back()->with('error', 'Template not found');
        }

        $html = File::get($htmlPath);

        // Read CSS
        $cssPath = storage_path("app/public/templates/css/{$template->slug}.css");
        $css = File::exists($cssPath) ? File::get($cssPath) : '';

        // Fill template
        $filledHtml = $this->fillTemplate($html, $css, $data);

        // Generate PDF
        $pdf = Pdf::loadHTML($filledHtml)->setPaper('A4', 'portrait');

        // ✅ FIXED: Direct save to correct path
        $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
        $directory = storage_path('app/public/resumes');

        // Create directory if needed
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Save PDF directly
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

        return redirect()->route('user.resumes.success', $resume->id);

    } catch (\Exception $e) {
        return back()->withInput()->with('error', $e->getMessage());
    }
}

/**
 * View PDF - FIXED VERSION
 */
public function view($id)
{
    $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

    $fullPath = storage_path('app/public/' . $resume->generated_pdf_path);

    if (!file_exists($fullPath)) {
        return redirect()->back()->with('error', 'PDF not found at: ' . $fullPath);
    }

    return response()->file($fullPath);
}

/**
 * Download PDF - FIXED VERSION
 */
public function download($id)
{
    $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

    $fullPath = storage_path('app/public/' . $resume->generated_pdf_path);

    if (!file_exists($fullPath)) {
        return redirect()->back()->with('error', 'PDF not found');
    }

    return response()->download($fullPath, 'my-resume.pdf');
}

/**
 * Show success page after generating resume
 */
public function success($id)
{
    $resume = UserResume::where('id', $id)
        ->where('user_id', auth()->id())
        ->with('template')
        ->firstOrFail();

    // Get available add-ons for upsell
    $addOns = \App\Models\AddOn::active()
        ->orderBy('sort_order')
        ->get();

    // Check which add-ons user has already purchased
    $purchasedAddOnIds = auth()->user()->userAddOns()
        ->where('status', 'active')
        ->pluck('add_on_id')
        ->toArray();

    // Filter out purchased add-ons
    $availableAddOns = $addOns->whereNotIn('id', $purchasedAddOnIds);

    return view('user.resumes.success', compact('resume', 'availableAddOns'));
}

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

        // Use OpenAI API or similar to generate professional experience content
        $prompt = "Generate a professional resume experience entry for someone who worked as a {$validated['job_title']} at {$validated['company']} for {$validated['years']} years";

        if ($validated['responsibilities']) {
            $prompt .= ". Key responsibilities: {$validated['responsibilities']}";
        }

        $prompt .= ". Format as bullet points with 3-4 achievement statements. Make it professional and impactful.";

        // Call OpenAI API
        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
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
            'junior' => 'junior-level',
            'mid' => 'mid-level',
            'senior' => 'senior-level',
        ];

        $prompt = "Generate a comprehensive skills list for a {$levelDescriptions[$validated['level']]} {$validated['role']}";

        if ($validated['fields']) {
            $prompt .= " with expertise in: {$validated['fields']}";
        }

        $prompt .= ". Include technical skills, programming languages, frameworks, tools, and soft skills. Format as comma-separated or bullet points. Make it professional and industry-relevant.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
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
            'graduation_year' => 'required|numeric|min:1950',
        ]);

        $prompt = "Generate a professional education entry for someone with a {$validated['degree']} in {$validated['field_of_study']} from {$validated['university']}, graduated in {$validated['graduation_year']}";

        $prompt .= ". Include relevant coursework, honors, GPA (if applicable), and achievements. Format professionally for a resume. Keep it concise but impressive.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

/**
 * Call OpenAI API to generate content
 */
private function callOpenAI($prompt)
{
    try {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            \Log::warning('OpenAI API key not configured');
            return null;
        }

        $client = new \GuzzleHttp\Client();

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
                        'content' => 'You are a professional resume writer. Generate clear, concise, and impactful resume content.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        return null;

    } catch (\Exception $e) {
        \Log::error('OpenAI API error: ' . $e->getMessage());
        return null;
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
        
        if ($validated['skills']) {
            $prompt .= ". Key skills: {$validated['skills']}";
        }
        
        if ($validated['goal']) {
            $prompt .= ". Career goal: {$validated['goal']}";
        }

        $prompt .= ". Make it professional, engaging, and suitable for a resume. It should highlight achievements and value proposition.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

}
