<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\PostedJob;
use App\Models\JobApplication;
use App\Models\JobCandidateMatch;
use App\Models\CompanyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $jobs = PostedJob::withCount('applications')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_jobs' => $jobs->count(),
            'featured_jobs' => $jobs->where('is_featured', true)->count(),
            'applications' => $jobs->sum('applications_count'),
        ];

        // Check if employer has AI matching access
        $hasAiMatching = $user->activeEmployerAddOns()
            ->whereHas('addOn', fn($q) => $q->where('type', 'ai_matching'))
            ->exists();

        // Get AI-matched candidates for employer's jobs (top matches)
        $aiMatches = collect();
        if ($hasAiMatching) {
            $aiMatches = JobCandidateMatch::with(['job', 'candidate', 'resume'])
                ->whereHas('job', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'shortlisted')
                ->orderByDesc('match_score')
                ->limit(10)
                ->get();
        }

        $packages = $this->getPackages();
        $addons = $this->getAddons();

        return view('company.dashboard', compact('jobs', 'packages', 'addons', 'stats', 'aiMatches', 'hasAiMatching'));
    }

    public function create()
    {
        $packages = $this->getPackages();
        $addons = $this->getAddons();

        return view('company.job-create', compact('packages', 'addons'));
    }

    public function jobs()
    {
        $user = Auth::user();

        $jobs = PostedJob::withCount('applications')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('company.jobs', compact('jobs'));
    }

    public function applications()
    {
        $user = Auth::user();

        $applications = JobApplication::with('job')
            ->whereHas('job', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('company.applications', compact('applications'));
    }

    public function applicationsForJob(PostedJob $job)
    {
        $user = Auth::user();

        \Log::info('Job owner check', [
            'job_id' => $job->id,
            'job_user_id' => $job->user_id,
            'auth_id' => $user->id
        ]);
        // Cast to int to avoid strict type mismatch (DB returns string IDs)
        abort_unless((int) $job->user_id === (int) $user->id, 403);

        $applications = $job->applications()->latest()->paginate(15);

        return view('company.applications', compact('applications', 'job'));
    }

    public function show(PostedJob $job)
    {
        $user = Auth::user();

        // Ensure employer owns the job
        abort_unless((int) $job->user_id === (int) $user->id, 403);

        return view('company.job-show', compact('job'));
    }

    public function aiMatching()
    {
        $user = Auth::user();

        // Check if employer has AI matching access
        $hasAiMatching = $user->activeEmployerAddOns()
            ->whereHas('addOn', fn($q) => $q->where('type', 'ai_matching'))
            ->exists();

        // Get employer's jobs with match counts
        $jobs = PostedJob::where('user_id', $user->id)
            ->where('source', 'company')
            ->withCount(['candidateMatches', 'candidateMatches as shortlisted_count' => function($q) {
                $q->where('status', 'shortlisted');
            }])
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total_jobs' => PostedJob::where('user_id', $user->id)->where('source', 'company')->count(),
            'total_matches' => JobCandidateMatch::whereHas('job', fn($q) => $q->where('user_id', $user->id))->count(),
            'shortlisted' => JobCandidateMatch::whereHas('job', fn($q) => $q->where('user_id', $user->id))->where('status', 'shortlisted')->count(),
        ];

        return view('company.ai-matching', compact('jobs', 'stats', 'hasAiMatching'));
    }

    public function aiMatchingForJob(PostedJob $job)
    {
        $user = Auth::user();

        // Verify job belongs to employer
        abort_unless((int) $job->user_id === (int) $user->id, 403);

        // Check if employer has AI matching access
        $hasAiMatching = $user->activeEmployerAddOns()
            ->whereHas('addOn', fn($q) => $q->where('type', 'ai_matching'))
            ->exists();

        if (!$hasAiMatching) {
            return redirect()->route('company.ai-matching')
                ->with('info', 'Please purchase the AI Matching add-on to view matches.');
        }

        // Check if employer has AI matching access
        $hasAiMatching = $user->activeEmployerAddOns()
            ->whereHas('addOn', fn($q) => $q->where('type', 'ai_matching'))
            ->exists();

        if (!$hasAiMatching) {
            return redirect()->route('company.addons')
                ->with('info', 'Please purchase the AI Matching add-on to access this feature.');
        }

        // Get matches for this specific job
        $matches = JobCandidateMatch::with(['candidate', 'resume'])
            ->where('job_id', $job->id)
            ->orderByDesc('match_score')
            ->orderByDesc('matched_at')
            ->paginate(20);

        $stats = [
            'total_matches' => $matches->total(),
            'shortlisted' => JobCandidateMatch::where('job_id', $job->id)->where('status', 'shortlisted')->count(),
            'contacted' => JobCandidateMatch::where('job_id', $job->id)->where('status', 'contacted')->count(),
            'pending' => JobCandidateMatch::where('job_id', $job->id)->where('status', 'pending')->count(),
        ];

        return view('company.ai-matching-job', compact('job', 'matches', 'stats', 'hasAiMatching'));
    }

    public function packages()
    {
        $packages = $this->getPackages();
        return view('company.packages', compact('packages'));
    }

    public function addons()
    {
        $user = Auth::user();
        $addons = $this->getAddons();

        // Get user's active addons
        $activeAddons = $user->activeEmployerAddOns()->with('addOn')->get()->pluck('addOn.slug')->toArray();

        return view('company.addons', compact('addons', 'activeAddons'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'salary' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_featured' => 'sometimes|boolean',
        ]);

        $tags = [];
        if (!empty($data['tags'])) {
            $tags = collect(explode(',', $data['tags']))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->values()
                ->all();
        }

        PostedJob::create([
            'user_id' => $user->id,
            'external_id' => 'company-' . Str::uuid(),
            'title' => $data['title'],
            'company' => $data['company'],
            'location' => $data['location'],
            'type' => $data['type'] ?? 'Full Time',
            'description' => $data['description'] ?? null,
            'salary' => $data['salary'] ?? null,
            'tags' => $tags ?: null,
            'posted_at' => now(),
            'source' => 'company',
            'url' => null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
        ]);

        return redirect()
            ->route('company.dashboard')
            ->with('success', 'Job posted successfully.');
    }

    public function packageCheckout($slug)
    {
        $packages = $this->getPackages();
        $package = collect($packages)->firstWhere('slug', $slug);

        if (!$package) {
            abort(404, 'Package not found');
        }

        return view('company.checkout', [
            'item' => $package,
            'type' => 'package',
        ]);
    }

    public function addonCheckout($slug)
    {
        $addons = $this->getAddons();
        $addon = collect($addons)->firstWhere('slug', $slug);

        if (!$addon) {
            abort(404, 'Add-on not found');
        }

        return view('company.checkout', [
            'item' => $addon,
            'type' => 'addon',
        ]);
    }

    public function submitManualPayment(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:package,addon',
            'item_slug' => 'required|string',
            'payment_proof' => 'required|image|max:5120', // 5MB max
            'bank_account_name' => 'required|string|max:255',
            'transfer_date' => 'required|date',
        ]);

        // Get item details
        $item = null;
        $itemName = '';
        $amount = 0;

        if ($validated['item_type'] === 'package') {
            $item = collect($this->getPackages())->firstWhere('slug', $validated['item_slug']);
            $itemName = $item['name'] ?? 'Unknown Package';
            $amount = $item['price'] ?? 0;
        } else {
            $item = collect($this->getAddons())->firstWhere('slug', $validated['item_slug']);
            $itemName = $item['name'] ?? 'Unknown Add-on';
            $amount = $item['price'] ?? 0;
        }

        // Store payment proof
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Create payment record
        CompanyPayment::create([
            'user_id' => Auth::id(),
            'item_type' => $validated['item_type'],
            'item_slug' => $validated['item_slug'],
            'item_name' => $itemName,
            'amount' => $amount,
            'payment_method' => 'manual',
            'status' => 'pending',
            'payment_proof' => $path,
            'bank_account_name' => $validated['bank_account_name'],
            'transfer_date' => $validated['transfer_date'],
        ]);

        return redirect()
            ->route('company.dashboard')
            ->with('success', 'Payment proof submitted successfully. Admin will review and approve within 24 hours.');
    }

    public function stripeCheckout(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:package,addon',
            'item_slug' => 'required|string',
        ]);

        // Get item details
        $item = null;
        $itemName = '';
        $amount = 0;

        if ($validated['item_type'] === 'package') {
            $item = collect($this->getPackages())->firstWhere('slug', $validated['item_slug']);
            $itemName = $item['name'] ?? 'Unknown Package';
            $amount = $item['price'] ?? 0;
        } else {
            $item = collect($this->getAddons())->firstWhere('slug', $validated['item_slug']);
            $itemName = $item['name'] ?? 'Unknown Add-on';
            $amount = $item['price'] ?? 0;
        }

        if (!$item || $amount == 0) {
            return back()->with('error', 'Invalid item selected.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Determine currency based on amount (IDR if > 1000, otherwise USD)
            $currency = $amount >= 1000 ? 'idr' : 'usd';
            $stripeAmount = $currency === 'idr' ? $amount : ($amount * 100); // IDR doesn't use decimals, USD uses cents

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $itemName,
                            'description' => $validated['item_type'] === 'package'
                                ? $item['jobs'] . ' job postings'
                                : ($item['description'] ?? ''),
                        ],
                        'unit_amount' => $stripeAmount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('company.payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('company.dashboard'),
                'client_reference_id' => Auth::id(),
                'metadata' => [
                    'user_id' => Auth::id(),
                    'item_type' => $validated['item_type'],
                    'item_slug' => $validated['item_slug'],
                    'item_name' => $itemName,
                ],
            ];

            $session = StripeSession::create($sessionData);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request)
    {
        if (!$request->has('session_id')) {
            return redirect()->route('company.dashboard')->with('error', 'Invalid session.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                $metadata = $session->metadata;

                // Get item details
                $item = null;
                $amount = 0;

                if ($metadata['item_type'] === 'package') {
                    $item = collect($this->getPackages())->firstWhere('slug', $metadata['item_slug']);
                    $amount = $item['price'] ?? 0;
                } else {
                    $item = collect($this->getAddons())->firstWhere('slug', $metadata['item_slug']);
                    $amount = $item['price'] ?? 0;
                }

                // Create payment record
                CompanyPayment::create([
                    'user_id' => $metadata['user_id'],
                    'item_type' => $metadata['item_type'],
                    'item_slug' => $metadata['item_slug'],
                    'item_name' => $metadata['item_name'],
                    'amount' => $amount,
                    'payment_method' => 'stripe',
                    'status' => 'approved', // Stripe payments are auto-approved
                    'stripe_session_id' => $session->id,
                    'stripe_payment_intent' => $session->payment_intent,
                ]);

                // If it's an add-on purchase, grant access via EmployerAddOn
                if ($metadata['item_type'] === 'addon') {
                    // Find the AddOn by slug
                    $addOn = \App\Models\AddOn::where('slug', $metadata['item_slug'])->first();

                    if ($addOn) {
                        // Create or update EmployerAddOn record
                        \App\Models\EmployerAddOn::updateOrCreate(
                            [
                                'employer_id' => $metadata['user_id'],
                                'add_on_id' => $addOn->id,
                            ],
                            [
                                'amount_paid' => $amount,
                                'payment_gateway' => 'stripe',
                                'payment_id' => $session->payment_intent,
                                'status' => 'active',
                                'purchased_at' => now(),
                                'expires_at' => null, // Lifetime access unless specified
                            ]
                        );
                    }
                }

                return redirect()
                    ->route('company.dashboard')
                    ->with('success', 'Payment successful! Your ' . $metadata['item_type'] . ' has been activated.');
            }

            return redirect()->route('company.dashboard')->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            return redirect()->route('company.dashboard')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function stripeCancel()
    {
        return redirect()
            ->route('company.dashboard')
            ->with('info', 'Payment was cancelled.');
    }

    private function getPackages()
    {
        return [
            [
                'name' => 'Starter',
                'jobs' => 5,
                'price' => 2000000,
                'slug' => 'jobs-5',
            ],
            [
                'name' => 'Growth',
                'jobs' => 10,
                'price' => 3500000,
                'slug' => 'jobs-10',
            ],
        ];
    }

    private function getAddons()
    {
        return [
            [
                'name' => 'AI Candidate Matching',
                'description' => 'Automatically match qualified candidates to your jobs within 30 minutes',
                'price' => 2500000,
                'period' => 'month',
                'slug' => 'ai-matching',
            ],
            [
                'name' => 'Featured job',
                'description' => 'Highlight a job for more visibility',
                'price' => 300000,
                'slug' => 'featured',
            ],
            [
                'name' => 'CV access pack',
                'description' => 'Access candidate CVs for one month',
                'price' => 1000000,
                'period' => 'month',
                'slug' => 'cv-access',
            ],
        ];
    }
}
