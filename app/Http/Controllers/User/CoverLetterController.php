<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CoverLetter;
use App\Models\CoverLetterTemplate;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CoverLetterController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function index()
    {
        $coverLetters = auth()->user()->coverLetters()
            ->where('is_deleted', false)
            ->latest()
            ->paginate(10);

        return view('user.cover-letters.index', compact('coverLetters'));
    }

    public function create()
    {
        $templates = CoverLetterTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Pre-fill user information
        $user = auth()->user();
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? '',
        ];

        return view('user.cover-letters.create', compact('templates', 'userData'));
    }

    /**
     * Generate cover letter with OpenAI
     */
    public function generateWithAI(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:50',
            'user_address' => 'required|string|max:500',
            'recipient_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'job_description' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        try {
            $generatedContent = $this->openAIService->generateCoverLetter($validated);

            return response()->json([
                'success' => true,
                'content' => $generatedContent,
                'message' => 'Cover letter generated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   public function store(Request $request)
{
    try {
        // $validated = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'user_name' => 'required|string|max:255',
        //     'user_email' => 'required|email|max:255',
        //     'user_phone' => 'required|string|max:50',
        //     'user_address' => 'required|string|max:500',
        //     'recipient_name' => 'required|string|max:255',
        //     'company_name' => 'required|string|max:255',
        //     'company_address' => 'required|string|max:500',
        //     'content' => 'required|string',
        // ]);

        // $validated['user_id'] = auth()->id();

        // CoverLetter::create($validated);

        // return redirect()->route('user.cover-letters.index')
        //     ->with('success', 'Cover letter created successfully!');

            // Correct ability check
    $this->authorize('create', CoverLetter::class);

    $data = $request->validate([
        'title' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:50',
            'user_address' => 'required|string|max:500',
            'recipient_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'content' => 'required|string',
    ]);

    $coverLetter = auth()->user()->coverLetters()->create($data);

    // Flash session to indicate cover letter module completed
    session()->flash('module_completed', 'cover_letter');

    return redirect()
        ->route('user.cover-letters.index')
        ->with('success', 'Cover letter created successfully.');


    } catch (\Exception $e) {
        dd([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
    public function view(CoverLetter $coverLetter)
    {
        $this->authorize('view', $coverLetter);

        return view('user.cover-letters.view', compact('coverLetter'));
    }

    public function edit(CoverLetter $coverLetter)
    {
        $this->authorize('update', $coverLetter);

        $templates = CoverLetterTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('user.cover-letters.edit', compact('coverLetter', 'templates'));
    }

    public function update(Request $request, CoverLetter $coverLetter)
    {
        $this->authorize('update', $coverLetter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:50',
            'user_address' => 'required|string|max:500',
            'recipient_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $coverLetter->update($validated);

        return redirect()->route('user.cover-letters.index')
            ->with('success', 'Cover letter updated successfully!');
    }

    public function destroy(CoverLetter $coverLetter)
    {
        $this->authorize('delete', $coverLetter);

        $coverLetter->update(['is_deleted' => true]);

        return redirect()->route('user.cover-letters.index')
            ->with('success', 'Cover letter deleted successfully!');
    }

    public function xdownload(CoverLetter $coverLetter)
    {
        $this->authorize('view', $coverLetter);

        $pdf = Pdf::loadView('user.cover-letters.pdf', compact('coverLetter'));

        return $pdf->download($coverLetter->title . '.pdf');
    }

    public function download(CoverLetter $coverLetter)
{
    $this->authorize('view', $coverLetter);

    // Use your existing print.blade.php for PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.cover-letters.print', compact('coverLetter'));

    return $pdf->download($coverLetter->title . '.pdf');
}



          public function print(CoverLetter $coverLetter)
    {
        // Authorize user
        $this->authorize('view', $coverLetter);

        // Load the print view (your existing blade)
        return view('user.cover-letters.print', compact('coverLetter'));
    }


}
