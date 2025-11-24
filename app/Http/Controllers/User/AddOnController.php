<?php

namespace App\Http\Controllers\User;

use App\Services\OpenAIService;
use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\UserAddOn;
use Illuminate\Http\Request;

class AddOnController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    
    /**
     * Show job search form
     */
    public function jobSearch(AddOn $addOn)
    {
        $user = auth()->user();
    
        if (!$user->hasPurchasedAddOn($addOn->id)) {
            return redirect()->route('user.add-ons.checkout', $addOn)
                ->with('error', 'Please purchase this add-on to access job search features.');
        }
    
        return view('user.add-ons.job-search', compact('addOn'));
    }
    
    /**
     * Generate job recommendations using AI
     */
    public function generateJobRecommendations(Request $request, AddOn $addOn)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
        ]);
    
        $user = auth()->user();
    
        if (!$user->hasPurchasedAddOn($addOn->id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }
    
        $jobTitle = $request->job_title;
        $location = $request->location;
        $skills = $request->skills ? array_map('trim', explode(',', $request->skills)) : [];
    
        try {
            $recommendations = $this->openAIService->generateJobRecommendations($jobTitle, $location, $skills);
            
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            \Log::error('Job Recommendations Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate recommendations. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Show interview prep form
     */
    public function interviewPrep(AddOn $addOn)
    {
        $user = auth()->user();
    
        if (!$user->hasPurchasedAddOn($addOn->id)) {
            return redirect()->route('user.add-ons.checkout', $addOn)
                ->with('error', 'Please purchase this add-on to access interview preparation.');
        }
    
        return view('user.add-ons.interview-prep', compact('addOn'));
    }

    /**
     * Generate interview prep using AI - FIXED VERSION
     */
    public function generateInterviewPrep(Request $request, AddOn $addOn)
    {
        // Validate input
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,mid,senior,executive',
            'company_type' => 'required|in:startup,corporate,nonprofit,government,consulting',
        ]);

        $user = auth()->user();

        // Check access
        if (!$user->hasPurchasedAddOn($addOn->id)) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        try {
            // Generate interview prep
            $interviewPrep = $this->openAIService->generateInterviewPrep(
                $validated['job_title'],
                $validated['experience_level'],
                $validated['company_type']
            );
            
            // Ensure data is properly formatted
            if (empty($interviewPrep) || !is_array($interviewPrep)) {
                throw new \Exception('Invalid response format from OpenAI service');
            }

            // Return JSON response with proper headers
            return response()->json([
                'success' => true,
                'data' => $interviewPrep
            ], 200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Interview Prep Controller Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'job_title' => $validated['job_title'] ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate interview prep. Please try again.',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display available add-ons
     */
    public function index()
    {
        $user = auth()->user();
        
        $addOns = AddOn::active()
            ->orderBy('sort_order')
            ->get();

        $purchasedAddOnIds = $user->userAddOns()
            ->where('status', 'active')
            ->pluck('add_on_id')
            ->toArray();

        return view('user.add-ons.index', compact('addOns', 'purchasedAddOnIds'));
    }

    /**
     * Show add-on details
     */
    public function show(AddOn $addOn)
    {
        $user = auth()->user();
        $hasPurchased = $user->hasPurchasedAddOn($addOn->id);

        return view('user.add-ons.show', compact('addOn', 'hasPurchased'));
    }

    /**
     * Show checkout page for add-on
     */
    public function checkout(AddOn $addOn)
    {
        $user = auth()->user();

        if ($user->hasPurchasedAddOn($addOn->id)) {
            return redirect()->route('user.add-ons.access', $addOn)
                ->with('info', 'You already have access to this add-on!');
        }

        return view('user.add-ons.checkout', compact('addOn'));
    }

    /**
     * Process add-on purchase
     */
    public function purchase(Request $request, AddOn $addOn)
    {
        $user = auth()->user();

        if ($user->hasPurchasedAddOn($addOn->id)) {
            return redirect()->route('user.add-ons.access', $addOn)
                ->with('info', 'You already have access to this add-on!');
        }

        $userAddOn = UserAddOn::create([
            'user_id' => $user->id,
            'add_on_id' => $addOn->id,
            'amount_paid' => $addOn->price,
            'payment_gateway' => $request->payment_method ?? 'stripe',
            'status' => 'pending',
            'purchased_at' => now(),
        ]);

        if ($request->payment_method === 'paypal') {
            return redirect()->route('user.add-ons.paypal-checkout', $userAddOn);
        }

        return redirect()->route('user.add-ons.stripe-checkout', $userAddOn);
    }

    /**
     * Stripe checkout for add-on
     */
    public function stripeCheckout(UserAddOn $userAddOn)
    {
        $addOn = $userAddOn->addOn;

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $addOn->name,
                        'description' => $addOn->description,
                    ],
                    'unit_amount' => $addOn->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('user.add-ons.payment-success', ['userAddOn' => $userAddOn->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
            'cancel_url' => route('user.add-ons.checkout', $addOn),
            'metadata' => [
                'user_add_on_id' => $userAddOn->id,
                'user_id' => auth()->id(),
                'add_on_id' => $addOn->id,
            ],
        ]);

        return redirect($session->url);
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request, UserAddOn $userAddOn)
    {
        $userAddOn->update([
            'status' => 'active',
            'payment_id' => $request->session_id,
        ]);

        return redirect()->route('user.add-ons.access', $userAddOn->addOn)
            ->with('success', 'Payment successful! You now have access to ' . $userAddOn->addOn->name);
    }

    /**
     * Access purchased add-on content
     */
    public function access(AddOn $addOn)
    {
        $user = auth()->user();

        $userAddOn = UserAddOn::where('user_id', $user->id)
            ->where('add_on_id', $addOn->id)
            ->where('status', 'active')
            ->first();

        if (!$userAddOn) {
            return redirect()->route('user.add-ons.show', $addOn)
                ->with('error', 'You need to purchase this add-on first to access it.');
        }

        return view('user.add-ons.access', compact('addOn', 'userAddOn'));
    }

    /**
     * View user's purchased add-ons
     */
    public function myAddOns()
    {
        $user = auth()->user();
        
        $addOns = $user->userAddOns()
            ->with('addOn')
            ->latest()
            ->get();

        return view('user.add-ons.my-add-ons', compact('addOns'));
    }
}