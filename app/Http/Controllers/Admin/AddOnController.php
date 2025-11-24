<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AddOnController extends Controller
{
    /**
     * Display a listing of add-ons
     */
    public function index()
    {
        $addOns = AddOn::withCount(['userAddOns', 'activePurchases'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($addOn) {
                $addOn->total_revenue = $addOn->userAddOns()->sum('amount_paid');
                return $addOn;
            });

        return view('admin.add-ons.index', compact('addOns'));
    }

    /**
     * Show the form for creating a new add-on
     */
    public function create()
    {
        return view('admin.add-ons.create');
    }

    /**
     * Store a newly created add-on - WITH DETAILED LOGGING
     */
    public function store(Request $request)
    {
        // Enable query log to see what's happening
        DB::enableQueryLog();
        
        Log::info('========================================');
        Log::info('ADD-ON STORE REQUEST STARTED');
        Log::info('========================================');
        Log::info('All Request Data:', $request->all());
        Log::info('Request Method:', ['method' => $request->method()]);
        Log::info('Request URL:', ['url' => $request->fullUrl()]);
        Log::info('CSRF Token:', ['token' => $request->input('_token')]);
        Log::info('Has is_active:', ['value' => $request->has('is_active')]);
        
        try {
            Log::info('Starting validation...');
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:add_ons',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'type' => 'required|in:job_links,interview_prep,custom',
                'features_text' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
            ]);
            
            Log::info('✓ Validation PASSED');
            Log::info('Validated Data:', $validated);

            // Handle is_active
            $validated['is_active'] = $request->has('is_active');
            Log::info('is_active set to:', ['value' => $validated['is_active']]);
            
            // Convert features_text to array
            if ($request->filled('features_text')) {
                $featuresText = $request->input('features_text');
                Log::info('Features text received:', ['text' => $featuresText]);
                
                $features = array_filter(
                    array_map('trim', explode("\n", $featuresText)),
                    function($item) {
                        return !empty($item);
                    }
                );
                $validated['features'] = array_values($features);
                Log::info('Features converted to array:', ['array' => $validated['features']]);
            } else {
                $validated['features'] = [];
                Log::info('No features provided, set to empty array');
            }
            
            // Remove features_text as it's not a DB column
            unset($validated['features_text']);
            
            // Set default sort_order if not provided
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = 0;
                Log::info('Sort order not provided, set to 0');
            }

            Log::info('Final data to be saved:', $validated);
            Log::info('Attempting to create AddOn...');
            
            // Create the add-on
            $addOn = AddOn::create($validated);
            
            Log::info('✓ AddOn created successfully!', [
                'id' => $addOn->id,
                'name' => $addOn->name,
                'slug' => $addOn->slug
            ]);
            
            // Log all queries executed
            $queries = DB::getQueryLog();
            Log::info('Database Queries Executed:', ['queries' => $queries]);

            Log::info('Redirecting to index with success message');
            Log::info('========================================');
            
            return redirect()
                ->route('admin.add-ons.index')
                ->with('success', 'Add-on created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('✗ VALIDATION FAILED');
            Log::error('Validation Errors:', $e->errors());
            Log::error('Failed Fields:', array_keys($e->errors()));
            Log::error('========================================');
            
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($e->errors())
                ->with('error', 'Validation failed. Please check the form.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('✗ DATABASE ERROR');
            Log::error('Error Message:', ['message' => $e->getMessage()]);
            Log::error('SQL:', ['sql' => $e->getSql() ?? 'N/A']);
            Log::error('Bindings:', ['bindings' => $e->getBindings() ?? []]);
            Log::error('========================================');
            
            return redirect()
                ->back()
                ->withInput($request->all())
                ->with('error', 'Database error: ' . $e->getMessage());
                
        } catch (\Exception $e) {
            Log::error('✗ UNEXPECTED ERROR');
            Log::error('Error Type:', ['type' => get_class($e)]);
            Log::error('Error Message:', ['message' => $e->getMessage()]);
            Log::error('Error File:', ['file' => $e->getFile()]);
            Log::error('Error Line:', ['line' => $e->getLine()]);
            Log::error('Stack Trace:', ['trace' => $e->getTraceAsString()]);
            Log::error('========================================');
            
            return redirect()
                ->back()
                ->withInput($request->all())
                ->with('error', 'Failed to create add-on: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified add-on
     */
    public function show(AddOn $addOn)
    {
        $addOn->load(['userAddOns.user']);
        
        $stats = [
            'total_purchases' => $addOn->userAddOns()->count(),
            'active_purchases' => $addOn->activePurchases()->count(),
            'total_revenue' => $addOn->userAddOns()->sum('amount_paid'),
            'this_month_revenue' => $addOn->userAddOns()
                ->whereMonth('purchased_at', now()->month)
                ->sum('amount_paid'),
        ];

        $recentPurchases = $addOn->userAddOns()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.add-ons.show', compact('addOn', 'stats', 'recentPurchases'));
    }

    /**
     * Show the form for editing the specified add-on
     */
    public function edit(AddOn $addOn)
    {
        return view('admin.add-ons.edit', compact('addOn'));
    }

    /**
     * Update the specified add-on
     */
    public function update(Request $request, AddOn $addOn)
    {
        Log::info('========================================');
        Log::info('ADD-ON UPDATE REQUEST', ['id' => $addOn->id]);
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:add_ons,slug,' . $addOn->id,
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'type' => 'required|in:job_links,interview_prep,custom',
                'features_text' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
            ]);

            $validated['is_active'] = $request->has('is_active');
            
            // Convert features_text to array
            if ($request->filled('features_text')) {
                $features = array_filter(
                    array_map('trim', explode("\n", $request->features_text)),
                    function($item) {
                        return !empty($item);
                    }
                );
                $validated['features'] = array_values($features);
            } else {
                $validated['features'] = [];
            }
            
            unset($validated['features_text']);
            
            $addOn->update($validated);

            Log::info('✓ AddOn updated successfully', ['id' => $addOn->id]);
            Log::info('========================================');

            return redirect()
                ->route('admin.add-ons.index')
                ->with('success', 'Add-on updated successfully!');

        } catch (\Exception $e) {
            Log::error('✗ Update failed:', ['error' => $e->getMessage()]);
            Log::error('========================================');
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update add-on: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified add-on
     */
    public function destroy(AddOn $addOn)
    {
        $addOn->delete();
        return redirect()->route('admin.add-ons.index')
            ->with('success', 'Add-on deleted successfully!');
    }

    /**
     * Toggle add-on active status
     */
    public function toggleStatus(AddOn $addOn)
    {
        $addOn->update(['is_active' => !$addOn->is_active]);
        return back()->with('success', 'Add-on status updated successfully!');
    }

    /**
     * View all purchases for an add-on
     */
    public function purchases(AddOn $addOn)
    {
        $purchases = $addOn->userAddOns()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.add-ons.purchases', compact('addOn', 'purchases'));
    }
}