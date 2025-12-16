<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\CompanyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $jobs = Job::withCount('applications')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_jobs' => $jobs->count(),
            'featured_jobs' => $jobs->where('is_featured', true)->count(),
            'applications' => $jobs->sum('applications_count'),
        ];

        $packages = $this->getPackages();
        $addons = $this->getAddons();

        return view('company.dashboard', compact('jobs', 'packages', 'addons', 'stats'));
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

        $jobs = Job::withCount('applications')
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

    public function applicationsForJob(Job $job)
    {
        $user = Auth::user();

        abort_unless($job->user_id === $user->id, 403);

        $applications = $job->applications()->latest()->paginate(15);

        return view('company.applications', compact('applications', 'job'));
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

        Job::create([
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
        // TODO: Implement Stripe checkout session creation
        $validated = $request->validate([
            'item_type' => 'required|in:package,addon',
            'item_slug' => 'required|string',
        ]);

        return redirect()
            ->route('company.dashboard')
            ->with('info', 'Stripe integration coming soon. Please use manual payment for now.');
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
