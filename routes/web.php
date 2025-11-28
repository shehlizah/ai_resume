<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\TemplateController;
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
use App\Http\Middleware\CheckActivePackage;

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
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

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
    // Payment Processing (ALWAYS ACCESSIBLE)
    // ==========================================
    Route::prefix('payment')->name('user.payment.')->group(function () {
        // Stripe
        Route::post('/stripe/checkout', [PaymentController::class, 'stripeCheckout'])->name('stripe.checkout');
        Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');

        // PayPal
        Route::post('/paypal/checkout', [PaymentController::class, 'paypalCheckout'])->name('paypal.checkout');
        Route::post('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
        Route::get('/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('paypal.cancel');
    });

    // ==========================================
    // Resume Management Routes
    // ==========================================
    Route::prefix('resumes')->name('user.resumes.')->group(function () {
        
        // UNPROTECTED - Viewing/Browsing (No Package Required)
        Route::get('/', [UserResumeController::class, 'index'])->name('index');
        Route::get('/view/{id}', [UserResumeController::class, 'view'])->name('view');
        Route::delete('/{id}', [UserResumeController::class, 'destroy'])->name('destroy');
        
        // PROTECTED - Creating/Downloading (Package Required)
        Route::middleware([CheckActivePackage::class])->group(function () {
            Route::get('/choose', [UserResumeController::class, 'chooseTemplate'])->name('choose');
            Route::get('/create', [UserResumeController::class, 'chooseTemplate'])->name('create');
            Route::get('/preview/{template_id}', [UserResumeController::class, 'preview'])->name('preview');
            Route::get('/fill/{template_id}', [UserResumeController::class, 'fillForm'])->name('fill');
            Route::post('/generate', [UserResumeController::class, 'generate'])->name('generate');
            Route::get('/success/{id}', [UserResumeController::class, 'success'])->name('success');
            Route::get('/download/{id}', [UserResumeController::class, 'download'])->name('download');
            
            // AI Generation Routes
            Route::post('/generate-experience-ai', [UserResumeController::class, 'generateExperienceAI'])->name('generate-experience-ai');
            Route::post('/generate-skills-ai', [UserResumeController::class, 'generateSkillsAI'])->name('generate-skills-ai');
            Route::post('/generate-education-ai', [UserResumeController::class, 'generateEducationAI'])->name('generate-education-ai');
            Route::post('/generate-summary-ai', [UserResumeController::class, 'generateSummaryAI'])->name('generate-summary-ai');
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
        
        // PROTECTED - Creating/Downloading (Package Required)
        Route::middleware([CheckActivePackage::class])->group(function () {
            // Creation routes
            Route::get('/create', [CoverLetterController::class, 'create'])->name('create');
            Route::post('/store', [CoverLetterController::class, 'store'])->name('store');
            
            // Template selection
            Route::get('/templates', [CoverLetterController::class, 'selectTemplate'])->name('select-template');
            Route::get('/templates/{template}/use', [CoverLetterController::class, 'createFromTemplate'])->name('create-from-template');
            
            // Editing
            Route::get('/{coverLetter}/edit', [CoverLetterController::class, 'edit'])->name('edit');
            Route::put('/{coverLetter}/update', [CoverLetterController::class, 'update'])->name('update');
            
            // Downloads and printing
            Route::get('/{coverLetter}/download', [CoverLetterController::class, 'download'])->name('download');
            Route::get('/{coverLetter}/print', [CoverLetterController::class, 'print'])->name('print');
            
            // AI Generation
            Route::post('/generate-ai', [CoverLetterController::class, 'generateWithAI'])->name('generate-ai');
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
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';