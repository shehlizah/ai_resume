<?php
use App\Models\AbandonedCart;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\AbandonedCartController;
use App\Http\Controllers\UserResumeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\StripeWebhookController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\CoverLetterController;
use App\Http\Controllers\User\JobFinderController;
use App\Http\Controllers\User\InterviewPrepController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\AbandonmentTrackingController;
use App\Http\Middleware\CheckActivePackage;
use function app;
use function response;
use function request;
use function view;
use function now;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
*/
Route::get('/debug/locale', function () {
    $service = app('\App\Services\GoogleTranslateService');
    $testText = 'Selamat datang';
    $translation = $service->translate($testText, 'en');
    return response()
        ->view('debug.locale', [
            'currentLocale' => app()->getLocale(),
            'testText' => $testText,
            'translation' => $translation,
            'acceptLanguage' => request()->header('Accept-Language'),
        ])
        ->header('Content-Type', 'text/html; charset=utf-8');
})->name('debug.locale');

/*
|--------------------------------------------------------------------------
| Language Switching Routes (No middleware needed)
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', [LocaleController::class, 'setLocale'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('frontend.pages.home');
})->name('home');

Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('packages');

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook (No Auth Required)
    |--------------------------------------------------------------------------
    */
    Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

    /*
    |--------------------------------------------------------------------------
    | Payment Processing Routes (No Auth Required - User kept from session)
    |--------------------------------------------------------------------------
    */
    Route::prefix('payment')->name('user.payment.')->group(function () {
        // Stripe
        Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');
        Route::post('/stripe/checkout', [PaymentController::class, 'stripeCheckout'])->name('stripe.checkout');

        // PayPal
        Route::post('/paypal/checkout', [PaymentController::class, 'paypalCheckout'])->name('paypal.checkout');
        Route::post('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
        Route::get('/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('paypal.cancel');
    });

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user,admin'])->group(function () {

    // ==========================================
    // User Dashboard
    // ==========================================
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard/stats', [UserDashboardController::class, 'getStats'])->name('user.dashboard.stats');

    // ==========================================
    // Settings
    // ==========================================
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/monetization', 'settings.monetization')->name('settings.monetization');

    // ==========================================
    // Pricing & Plans (ALWAYS ACCESSIBLE)
    // ==========================================
    Route::get('/user/pricing', [SubscriptionController::class, 'pricing'])->name('user.pricing');

    // ==========================================
    // Subscription Management (ALWAYS ACCESSIBLE)
    // ==========================================
    Route::prefix('subscription')->name('user.subscription.')->group(function () {
        Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/change-billing', [SubscriptionController::class, 'changeBillingPeriod'])->name('change-billing');
    });

    // ==========================================
    // Resume Management Routes
    // ==========================================
    Route::prefix('resumes')->name('user.resumes.')->group(function () {

        // UNPROTECTED - Viewing/Browsing (No Package Required)
        Route::get('/', [UserResumeController::class, 'index'])->name('index');
        Route::get('/view/{id}', [UserResumeController::class, 'view'])->name('view');
        Route::delete('/{id}', [UserResumeController::class, 'destroy'])->name('destroy');
        Route::get('/choose', [UserResumeController::class, 'chooseTemplate'])->name('choose');
        Route::get('/create', [UserResumeController::class, 'chooseTemplate'])->name('create');
        Route::get('/preview/{template_id}', [UserResumeController::class, 'preview'])->name('preview');
        Route::get('/print-preview/{id}', [UserResumeController::class, 'printPreview'])->name('print-preview');
        Route::get('/fill/{template_id}', [UserResumeController::class, 'fillForm'])->name('fill');
        Route::post('/generate', [UserResumeController::class, 'generate'])->name('generate');
        Route::get('/success/{id}', [UserResumeController::class, 'success'])->name('success');


        // AI Generation Routes
        Route::post('/generate-experience-ai', [UserResumeController::class, 'generateExperienceAI'])->name('generate-experience-ai');
        Route::post('/generate-skills-ai', [UserResumeController::class, 'generateSkillsAI'])->name('generate-skills-ai');
        Route::post('/generate-education-ai', [UserResumeController::class, 'generateEducationAI'])->name('generate-education-ai');
        Route::post('/generate-summary-ai', [UserResumeController::class, 'generateSummaryAI'])->name('generate-summary-ai');

        // Temporary file upload (for job finder and interview prep)
        Route::post('/upload-temp', [UserResumeController::class, 'uploadTemporary'])->name('upload-temp');

        // PROTECTED - Creating/Downloading (Package Required)
        Route::middleware([CheckActivePackage::class])->group(function () {
            Route::get('/download/{id}', [UserResumeController::class, 'download'])->name('download');
        });
    });

    // Legacy resume route (backwards compatibility)
    Route::get('/user/resumes', [UserResumeController::class, 'index'])->name('user.resumes');

    // ==========================================
    // Cover Letter Management Routes
    // ==========================================
    Route::prefix('cover-letters')->name('user.cover-letters.')->group(function () {

        // UNPROTECTED - Viewing/Browsing (No Package Required)
        Route::get('/', [CoverLetterController::class, 'index'])->name('index');
        Route::get('/{coverLetter}/view', [CoverLetterController::class, 'view'])->name('view');
        Route::delete('/{coverLetter}/destroy', [CoverLetterController::class, 'destroy'])->name('destroy');
       // Creation routes
        Route::get('/create', [CoverLetterController::class, 'create'])->name('create');
        Route::post('/store', [CoverLetterController::class, 'store'])->name('store');

        // Template selection
        Route::get('/templates', [CoverLetterController::class, 'selectTemplate'])->name('select-template');
        Route::get('/templates/{template}/use', [CoverLetterController::class, 'createFromTemplate'])->name('create-from-template');

        // Editing
        Route::get('/{coverLetter}/edit', [CoverLetterController::class, 'edit'])->name('edit');
        Route::put('/{coverLetter}/update', [CoverLetterController::class, 'update'])->name('update');
        // AI Generation
        Route::post('/generate-ai', [CoverLetterController::class, 'generateWithAI'])->name('generate-ai');

        // PROTECTED - Creating/Downloading (Package Required)
        Route::middleware([CheckActivePackage::class])->group(function () {
             // Downloads and printing
        Route::get('/{coverLetter}/download', [CoverLetterController::class, 'download'])->name('download');
        Route::get('/{coverLetter}/print', [CoverLetterController::class, 'print'])->name('print');



        });
    });

    // ==========================================
    // Job Finder Routes
    // ==========================================
    Route::prefix('jobs')->name('user.jobs.')->group(function () {
        // Recommended jobs (FREE)
        Route::get('/recommended', [JobFinderController::class, 'recommended'])->name('recommended');
        Route::post('/recommended', [JobFinderController::class, 'generateRecommended'])->name('recommended');

        // Search by location (FREE)
        Route::get('/by-location', [JobFinderController::class, 'byLocation'])->name('by-location');
        Route::post('/by-location', [JobFinderController::class, 'generateByLocation'])->name('by-location');

        // Reset session limit (FREE)
        Route::post('/reset-session', [JobFinderController::class, 'resetSessionLimit'])->name('reset-session');

        // Apply to job (FREE with limits)
        Route::post('/{jobId}/apply', [JobFinderController::class, 'applyJob'])->name('apply');
    });

    // ==========================================
    // Interview Prep Routes
    // ==========================================
    Route::prefix('interview')->name('user.interview.')->group(function () {
        // NEW: AI Interview Prep with Resume Upload (FREE + PRO)
        Route::get('/prep', [InterviewPrepController::class, 'prep'])->name('prep');
        Route::post('/prep/generate', [InterviewPrepController::class, 'generatePrep'])->name('generate-prep');

        // Practice questions (FREE)
        Route::get('/questions', [InterviewPrepController::class, 'questions'])->name('questions');

        // AI Mock Interview (PRO)
        Route::middleware([CheckActivePackage::class])->group(function () {
            Route::get('/ai-practice', [InterviewPrepController::class, 'aiPractice'])->name('ai-practice');
            Route::post('/ai-practice/start', [InterviewPrepController::class, 'startAIPractice'])->name('ai-practice-start');
            Route::post('/ai-practice/answer', [InterviewPrepController::class, 'submitAnswer'])->name('ai-practice-answer');
            Route::get('/ai-results/{sessionId}', [InterviewPrepController::class, 'aiResults'])->name('ai-results');
        });

        // Expert booking (PRO)
        Route::middleware([CheckActivePackage::class])->group(function () {
            Route::get('/expert', [InterviewPrepController::class, 'bookExpert'])->name('expert');
            Route::get('/my-sessions', [InterviewPrepController::class, 'mySessions'])->name('my-sessions');
        });
    });

    // ==========================================
    // Add-Ons Management
    // ==========================================
    Route::prefix('add-ons')->name('user.add-ons.')->group(function () {
        // Browsing add-ons (no package required)
        Route::get('/', [\App\Http\Controllers\User\AddOnController::class, 'index'])->name('index');
        Route::get('/{addOn}', [\App\Http\Controllers\User\AddOnController::class, 'show'])->name('show');

        // My purchased add-ons
        Route::get('/my-add-ons', [\App\Http\Controllers\User\AddOnController::class, 'myAddOns'])->name('my-add-ons');

        // Checkout (no package required - add-ons are separate purchases)
        Route::get('/{addOn}/checkout', [\App\Http\Controllers\User\AddOnController::class, 'checkout'])->name('checkout');
        Route::post('/{addOn}/purchase', [\App\Http\Controllers\User\AddOnController::class, 'purchase'])->name('purchase');

        // Payment processing
        Route::get('/payment/{userAddOn}/stripe', [\App\Http\Controllers\User\AddOnController::class, 'stripeCheckout'])->name('stripe-checkout');
        Route::get('/payment/{userAddOn}/success', [\App\Http\Controllers\User\AddOnController::class, 'paymentSuccess'])->name('payment-success');

        // Access purchased content (requires purchase, not package)
        Route::get('/{addOn}/access', [\App\Http\Controllers\User\AddOnController::class, 'access'])->name('access');

        // AI-Powered Features
        Route::get('/{addOn}/job-search', [\App\Http\Controllers\User\AddOnController::class, 'jobSearch'])->name('job-search');
        Route::post('/{addOn}/generate-jobs', [\App\Http\Controllers\User\AddOnController::class, 'generateJobRecommendations'])->name('generate-jobs');

        Route::get('/{addOn}/interview-prep', [\App\Http\Controllers\User\AddOnController::class, 'interviewPrep'])->name('interview-prep');
        Route::post('/{addOn}/generate-interview', [\App\Http\Controllers\User\AddOnController::class, 'generateInterviewPrep'])->name('generate-interview');
    });
});

// ==========================================
// Company Dashboard (Job Posting)
// ==========================================
Route::middleware(['auth', 'role:employer'])->prefix('company')->name('company.')->group(function () {
    // Use standard model binding for PostedJob; owner check enforced in controller
    Route::model('job', \App\Models\PostedJob::class);

    Route::get('/dashboard', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jobs/create', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'store'])->name('jobs.store');
    Route::get('/jobs', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{job}', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'update'])->name('jobs.update');
    Route::get('/applications', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'applications'])->name('applications.index');
    Route::get('/jobs/{job}/applications', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'applicationsForJob'])->name('jobs.applications');

    // AI Matching & Packages Pages
    Route::get('/ai-matching', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'aiMatching'])->name('ai-matching');
    Route::get('/ai-matching/candidates', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'viewAllMatches'])->name('ai-matching.candidates');
    Route::get('/ai-matching/{job}', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'aiMatchingForJob'])->name('ai-matching.job');
    Route::get('/ai-matching/{job}/candidate/{match}/resume', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'downloadMatchResume'])->name('ai-matching.candidate.resume');
    Route::post('/ai-matching/{job}/trigger-match', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'triggerManualMatching'])->name('ai-matching.trigger');
    Route::get('/packages', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'packages'])->name('packages');
    Route::get('/addons', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'addons'])->name('addons');

    // Package & Add-on Checkout
    Route::get('/packages/{slug}/checkout', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'packageCheckout'])->name('packages.checkout');
    Route::get('/addons/{slug}/checkout', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'addonCheckout'])->name('addons.checkout');
    Route::post('/payment/manual', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'submitManualPayment'])->name('payment.manual');
    Route::post('/payment/stripe', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'stripeCheckout'])->name('payment.stripe');
    Route::get('/payment/stripe/success', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'stripeSuccess'])->name('payment.stripe.success');
    Route::get('/payment/stripe/cancel', [\App\Http\Controllers\Company\CompanyDashboardController::class, 'stripeCancel'])->name('payment.stripe.cancel');

    // Debug route - remove after testing
    Route::get('/debug-addons', function() {
        $user = Auth::user();
        $allAddOns = \App\Models\EmployerAddOn::where('employer_id', $user->id)->with('addOn')->get();
        $activeAddOns = $user->activeEmployerAddOns()->with('addOn')->get();

        return response()->json([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'all_employer_addons' => $allAddOns->map(fn($ea) => [
                'id' => $ea->id,
                'add_on_id' => $ea->add_on_id,
                'status' => $ea->status,
                'expires_at' => $ea->expires_at?->toDateTimeString(),
                'addon_name' => $ea->addOn?->name,
                'addon_slug' => $ea->addOn?->slug,
            ]),
            'active_addons' => $activeAddOns->map(fn($ea) => [
                'addon_name' => $ea->addOn?->name,
                'addon_slug' => $ea->addOn?->slug,
            ]),
            'has_ai_matching' => $user->hasAiMatchingAddOn(),
        ]);
    })->name('debug-addons');

    // Temp route: Trigger matching for old jobs
    Route::get('/trigger-old-job-matching', function() {
        $user = Auth::user();
        $jobs = \App\Models\PostedJob::where('user_id', $user->id)
            ->where('source', 'company')
            ->whereDoesntHave('candidateMatches')
            ->get();

        $count = 0;
        foreach ($jobs as $job) {
            \App\Jobs\MatchCandidatesJob::dispatch($job)->delay(now()->addMinutes(1));
            $count++;
        }

        return response()->json([
            'message' => "Queued matching for $count old jobs",
            'jobs_processed' => $count,
        ]);
    })->name('trigger-old-job-matching');

    // Cleanup route: Remove matches with empty resumes
    Route::get('/cleanup-empty-resume-matches', function() {
        $user = Auth::user();

        // Find all matches for this employer's jobs where the resume has no data
        $deleted = \App\Models\JobCandidateMatch::whereHas('job', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('resume', fn($q) => $q->where(function($sub) {
                $sub->whereNull('data')
                    ->orWhere('data', '{}')
                    ->orWhere('data', '');
            }))
            ->delete();

        return response()->json([
            'message' => "Removed $deleted matches with empty resumes",
            'matches_removed' => $deleted,
        ]);
    })->name('cleanup-empty-matches');
});

// Job applications (candidates)
Route::middleware(['auth'])->group(function () {
    Route::get('/jobs/{job}/apply', [\App\Http\Controllers\JobApplicationController::class, 'show'])->name('jobs.apply.show');
    Route::post('/jobs/{job}/apply', [\App\Http\Controllers\JobApplicationController::class, 'store'])->name('jobs.apply.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // Resume Template Management
    // ==========================================
    Route::resource('templates', TemplateController::class)->except(['show']);
    Route::get('templates/{id}/preview', [TemplateController::class, 'preview'])->name('templates.preview');
    Route::post('templates/{id}/preview-live', [TemplateController::class, 'previewLive'])->name('templates.preview-live');
    Route::post('templates/{id}/toggle-active', [TemplateController::class, 'toggleActive'])->name('templates.toggle-active');
    Route::post('templates/{id}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');

    // ==========================================
    // User Management
    // ==========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/toggle-lifetime-access', [UserController::class, 'toggleLifetimeAccess'])->name('toggle-lifetime-access');
        Route::delete('/{userId}/resumes/{resumeId}', [UserController::class, 'deleteResume'])->name('delete-resume');
        Route::get('/{userId}/resumes/{resumeId}/download', [UserController::class, 'downloadResume'])->name('download-resume');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
    });

    // ==========================================
    // Subscription Plans Management
    // ==========================================
    Route::resource('subscription-plans', SubscriptionPlanController::class);
    Route::post('subscription-plans/{subscriptionPlan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('subscription-plans.toggle-status');

    // ==========================================
    // Subscriptions Management
    // ==========================================
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [AdminSubscriptionController::class, 'index'])->name('index');
        Route::get('/{subscription}', [AdminSubscriptionController::class, 'show'])->name('show');
        Route::post('/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('activate');
    });

    // ==========================================
    // Payments Management
    // ==========================================
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('reject');
        Route::get('/export/csv', [AdminPaymentController::class, 'export'])->name('export');
    });

    // ==========================================
    // Company Payments Management
    // ==========================================
    Route::prefix('company-payments')->name('company-payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/approve', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'reject'])->name('reject');
        Route::get('/{payment}/download-proof', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'downloadProof'])->name('download-proof');
        Route::get('/{payment}/view-proof', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'viewProof'])->name('view-proof');
        Route::get('/{payment}/debug-proof', [\App\Http\Controllers\Admin\CompanyPaymentController::class, 'debugProof'])->name('debug-proof');
    });

    // ==========================================
    // Cover Letters Management
    // ==========================================
    Route::prefix('cover-letters')->name('cover-letters.')->group(function () {
        // Dashboard & Statistics
        Route::get('/', [\App\Http\Controllers\Admin\CoverLetterController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\CoverLetterController::class, 'statistics'])->name('statistics');

        // Templates Management
        Route::get('/templates', [\App\Http\Controllers\Admin\CoverLetterController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\Admin\CoverLetterController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\Admin\CoverLetterController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}', [\App\Http\Controllers\Admin\CoverLetterController::class, 'showTemplate'])->name('templates.show');
        Route::get('/templates/{template}/edit', [\App\Http\Controllers\Admin\CoverLetterController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [\App\Http\Controllers\Admin\CoverLetterController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\CoverLetterController::class, 'deleteTemplate'])->name('templates.delete');
        Route::post('/templates/{template}/toggle', [\App\Http\Controllers\Admin\CoverLetterController::class, 'toggleTemplateStatus'])->name('templates.toggle');
        Route::post('/templates/{template}/duplicate', [\App\Http\Controllers\Admin\CoverLetterController::class, 'duplicateTemplate'])->name('templates.duplicate');
        Route::post('/templates/bulk-action', [\App\Http\Controllers\Admin\CoverLetterController::class, 'bulkTemplateAction'])->name('templates.bulk-action');

        // User Cover Letters Management
        Route::get('/user-cover-letters', [\App\Http\Controllers\Admin\CoverLetterController::class, 'userCoverLetters'])->name('user-cover-letters');
        Route::get('/user-cover-letters/{coverLetter}', [\App\Http\Controllers\Admin\CoverLetterController::class, 'viewCoverLetter'])->name('view-cover-letter');
        Route::delete('/user-cover-letters/{coverLetter}', [\App\Http\Controllers\Admin\CoverLetterController::class, 'deleteCoverLetter'])->name('delete-cover-letter');
        Route::post('/user-cover-letters/{coverLetter}/restore', [\App\Http\Controllers\Admin\CoverLetterController::class, 'restore'])->name('restore');
        Route::delete('/user-cover-letters/{coverLetter}/permanent', [\App\Http\Controllers\Admin\CoverLetterController::class, 'permanentDelete'])->name('permanent-delete');

        // Export
        Route::get('/export/cover-letters', [\App\Http\Controllers\Admin\CoverLetterController::class, 'exportCoverLetters'])->name('export.cover-letters');
        Route::get('/export/templates', [\App\Http\Controllers\Admin\CoverLetterController::class, 'exportTemplates'])->name('export.templates');
    });

    // ==========================================
    // Add-Ons Management
    // ==========================================
    Route::resource('add-ons', \App\Http\Controllers\Admin\AddOnController::class);
    Route::post('add-ons/{addOn}/toggle-status', [\App\Http\Controllers\Admin\AddOnController::class, 'toggleStatus'])
        ->name('add-ons.toggle-status');
    Route::get('add-ons/{addOn}/purchases', [\App\Http\Controllers\Admin\AddOnController::class, 'purchases'])
        ->name('add-ons.purchases');

    // ==========================================
    // Abandoned Carts Management
    // ==========================================
    Route::prefix('abandoned-carts')->name('abandoned-carts.')->group(function () {
        Route::get('/', [AbandonedCartController::class, 'index'])->name('index');
        Route::get('/{id}', [AbandonedCartController::class, 'show'])->name('show');
        Route::delete('/{id}', [AbandonedCartController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/mark-completed', [AbandonedCartController::class, 'markCompleted'])->name('mark-completed');
        Route::post('/{id}/send-reminder', [AbandonedCartController::class, 'sendReminder'])->name('send-reminder');
    });

    // ==========================================
    // Job Finder Management
    // ==========================================
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/user-activity', [\App\Http\Controllers\Admin\AdminJobController::class, 'userActivity'])->name('user-activity');
        Route::get('/api-settings', [\App\Http\Controllers\Admin\AdminJobController::class, 'apiSettings'])->name('api-settings');
        Route::post('/api-settings', [\App\Http\Controllers\Admin\AdminJobController::class, 'updateApiSettings'])->name('update-api-settings');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AdminJobController::class, 'statistics'])->name('statistics');
    });

    // ==========================================
    // Interview Prep Management
    // ==========================================
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/sessions', [\App\Http\Controllers\Admin\AdminInterviewController::class, 'sessions'])->name('sessions');
        Route::delete('/sessions/{sessionId}', [\App\Http\Controllers\Admin\AdminInterviewController::class, 'deleteSession'])->name('delete-session');
        Route::get('/questions', [\App\Http\Controllers\Admin\AdminInterviewController::class, 'questions'])->name('questions');
        Route::get('/settings', [\App\Http\Controllers\Admin\AdminInterviewController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AdminInterviewController::class, 'updateSettings'])->name('update-settings');
    });

    // ==========================================
    // Debug Routes (Admin Only)
    // ==========================================
    if (app()->environment('local', 'staging')) {
        Route::get('/templates/{id}/debug', function($id) {
            $template = \App\Models\Template::findOrFail($id);

            $issues = [];
            $warnings = [];

            // Check HTML content
            if (empty($template->html_content)) {
                $issues[] = '❌ Template has NO HTML content';
            } elseif (strlen($template->html_content) < 100) {
                $warnings[] = '⚠️ Template has very little HTML content (' . strlen($template->html_content) . ' characters)';
            }

            // Check CSS content
            if (empty($template->css_content)) {
                $warnings[] = '⚠️ Template has no CSS styling';
            }

            // Check for problematic CSS
            if (!empty($template->css_content)) {
                if (strpos($template->css_content, 'display: none') !== false) {
                    $warnings[] = '⚠️ CSS contains "display: none" - might hide content';
                }
                if (strpos($template->css_content, 'opacity: 0') !== false) {
                    $warnings[] = '⚠️ CSS contains "opacity: 0" - might hide content';
                }
                if (strpos($template->css_content, 'visibility: hidden') !== false) {
                    $warnings[] = '⚠️ CSS contains "visibility: hidden" - might hide content';
                }
            }

            // Find placeholders
            $placeholders = [];
            if (!empty($template->html_content)) {
                preg_match_all('/\{\{([^}]+)\}\}/', $template->html_content, $matches);
                $placeholders = array_unique($matches[1]);
            }

            return view('admin.templates.debug', compact('template', 'issues', 'warnings', 'placeholders'));
        })->name('templates.debug');
    }
});

/*
|--------------------------------------------------------------------------
| Test/Debug Routes (Local Only)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/test-starter-templates', function() {
        return response()->json([
            'count' => \App\Models\Template::count(),
            'templates' => \App\Models\Template::take(3)->get(['id', 'name', 'category'])
        ]);
    });

    Route::get('/debug-template/{id}', function($id) {
        $template = \App\Models\Template::findOrFail($id);
        $html = $template->html_content . '<style>' . $template->css_content . '</style>';

        // Check what CSS features are in the template
        $hasCSSGrid = (stripos($html, 'display: grid') !== false || stripos($html, 'display:grid') !== false);
        $hasFlexbox = (stripos($html, 'display: flex') !== false || stripos($html, 'display:flex') !== false);
        $hasGoogleFonts = (stripos($html, 'fonts.googleapis.com') !== false);
        $hasTransform = (stripos($html, 'transform:') !== false);
        $hasClipPath = (stripos($html, 'clip-path') !== false);

        return response()->json([
            'template_id' => $template->id,
            'template_name' => $template->name,
            'html_length' => strlen($html),
            'css_issues' => [
                'has_css_grid' => $hasCSSGrid ? '❌ YES - NOT COMPATIBLE' : '✅ NO',
                'has_flexbox' => $hasFlexbox ? '⚠️ YES - LIMITED SUPPORT' : '✅ NO',
                'has_google_fonts' => $hasGoogleFonts ? '❌ YES - NOT COMPATIBLE' : '✅ NO',
                'has_transform' => $hasTransform ? '❌ YES - NOT COMPATIBLE' : '✅ NO',
                'has_clip_path' => $hasClipPath ? '❌ YES - NOT COMPATIBLE' : '✅ NO',
            ],
            'dompdf_compatible' => (!$hasCSSGrid && !$hasGoogleFonts && !$hasTransform && !$hasClipPath),
            'recommendation' => (!$hasCSSGrid && !$hasGoogleFonts && !$hasTransform && !$hasClipPath)
                ? '✅ This template should work with DomPDF'
                : '❌ Use the DomPDF-compatible version instead'
        ]);
    })->middleware('auth');
}

/*
|--------------------------------------------------------------------------
| Cart Abandonment Tracking Routes
|--------------------------------------------------------------------------
*/
Route::post('/api/abandonment/track-signup', [AbandonmentTrackingController::class, 'trackSignupStart'])->name('abandonment.track-signup');
Route::post('/api/abandonment/track-resume', [AbandonmentTrackingController::class, 'trackResumeStart'])->middleware('auth')->name('abandonment.track-resume');
Route::get('/api/abandonment/stats', [AbandonmentTrackingController::class, 'getStats'])->middleware('auth', 'role:admin')->name('abandonment.stats');

/*--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';



Route::get('/debug/abandoned-cart/{type}', function ($type) {
    $cart = AbandonedCart::where('type', $type)
        ->where('status', 'abandoned')
        ->where('user_id', '!=', null)
        ->orderByDesc('id')
        ->first();
    if (!$cart) {
        return 'No abandoned cart found for type: ' . $type;
    }
    $user = $cart->user;
    if (!$user && $type === 'signup') {
        $email = $cart->session_data['email'] ?? null;
        if ($email) {
            $reminder = new \App\Notifications\IncompleteSignupReminder($cart);
            $recoveryCount = $cart->recovery_email_sent_count + 1;
            $mailMessage = $reminder->buildMailMessage('there', $recoveryCount);
            \Mail::to($email)->send(new class($mailMessage) extends \Illuminate\Mail\Mailable {
                public $mailMessage;
                public function __construct($mailMessage) { $this->mailMessage = $mailMessage; }
                public function build() {
                    $this->subject($this->mailMessage->subject);
                    $this->view('emails.default')->with([
                        'greeting' => $this->mailMessage->greeting,
                        'lines' => $this->mailMessage->introLines,
                        'actionText' => $this->mailMessage->actionText,
                        'actionUrl' => $this->mailMessage->actionUrl,
                        'outroLines' => $this->mailMessage->outroLines,
                        'salutation' => $this->mailMessage->salutation,
                    ]);
                    return $this;
                }
            });
            $cart->markRecoveryEmailSent();
            return 'IncompleteSignupReminder sent directly to ' . $email;
        } else {
            return 'No user or email found for cart ID: ' . $cart->id;
        }
    }
    if (!$user) {
        return 'No user found for cart ID: ' . $cart->id;
    }
    if ($type === 'pdf_preview') {
        $user->notify(new \App\Notifications\PdfPreviewUpgradeReminder($cart));
        return 'PdfPreviewUpgradeReminder sent to ' . $user->email;
    } elseif ($type === 'signup') {
        $user->notify(new \App\Notifications\IncompleteSignupReminder($cart));
        return 'IncompleteSignupReminder sent to ' . $user->email;
    } elseif ($type === 'resume') {
        $user->notify(new \App\Notifications\IncompleteResumeReminder($cart));
        return 'IncompleteResumeReminder sent to ' . $user->email;
    } else {
        return 'Unknown cart type: ' . $type;
    }
});
