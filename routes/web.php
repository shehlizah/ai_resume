<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\TemplateStarterController;
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
    
    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard/stats', [UserDashboardController::class, 'getStats'])->name('user.dashboard.stats');

    // Settings
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');

    // Pricing & Plans
    Route::get('/user/pricing', [SubscriptionController::class, 'pricing'])->name('user.pricing');

    // ==========================================
    // Subscription Management
    // ==========================================
    Route::prefix('subscription')->name('user.subscription.')->group(function () {
        Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/change-billing', [SubscriptionController::class, 'changeBillingPeriod'])->name('change-billing');
    });

    // ==========================================
    // Payment Processing
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
    Route::prefix('user/resumes')->name('user.resumes.')->group(function () {
        Route::get('/', [UserResumeController::class, 'index'])->name('index');
        Route::get('/choose', [UserResumeController::class, 'chooseTemplate'])->name('choose');
        Route::get('/preview/{template_id}', [UserResumeController::class, 'preview'])->name('preview');
        Route::get('/fill/{template_id}', [UserResumeController::class, 'fillForm'])->name('fill');
        Route::post('/generate', [UserResumeController::class, 'generate'])->name('generate');
        Route::get('/success/{id}', [UserResumeController::class, 'success'])->name('success');
        Route::get('/view/{id}', [UserResumeController::class, 'view'])->name('view');
        Route::get('/download/{id}', [UserResumeController::class, 'download'])->name('download');
        Route::delete('/{id}', [UserResumeController::class, 'destroy'])->name('destroy');
    });

    // Legacy resume routes (for backwards compatibility)
    Route::get('/resumes', [UserResumeController::class, 'index'])->name('user.resumes');
    Route::get('/resumes/create', [UserResumeController::class, 'chooseTemplate'])->name('user.resumes.create');

    // ==========================================
    // Cover Letter Management Routes
    // ==========================================
    Route::prefix('cover-letters')->name('user.cover-letters.')->group(function () {
        Route::get('/', [CoverLetterController::class, 'index'])->name('index');
        Route::get('/create', [CoverLetterController::class, 'create'])->name('create');
        Route::post('/store', [CoverLetterController::class, 'store'])->name('store');
        // Route::post('/store', [\App\Http\Controllers\User\CoverLetterController::class, 'store'])->name('user.cover-letters.store');
        // AI Generation Route - IMPORTANT: Must be before {coverLetter} routes
        Route::post('/generate-ai', [CoverLetterController::class, 'generateWithAI'])->name('generate-ai');
        
        // Template selection routes
        Route::get('/templates', [CoverLetterController::class, 'selectTemplate'])->name('select-template');
        Route::get('/templates/{template}/use', [CoverLetterController::class, 'createFromTemplate'])->name('create-from-template');
        
        // Individual cover letter routes
        Route::get('/{coverLetter}/view', [CoverLetterController::class, 'view'])->name('view');
        Route::get('/{coverLetter}/print', [CoverLetterController::class, 'print'])->name('print');

        // Route::get('/{coverLetter}/print', [CoverLetterController::class, 'print'])->name('print');
        Route::get('/{coverLetter}/edit', [CoverLetterController::class, 'edit'])->name('edit');
        Route::put('/{coverLetter}/update', [CoverLetterController::class, 'update'])->name('update');
        Route::delete('/{coverLetter}/destroy', [CoverLetterController::class, 'destroy'])->name('destroy');
        Route::get('/{coverLetter}/download', [CoverLetterController::class, 'download'])->name('download');
    });

    // ==========================================
    // Add-Ons Purchase & Access
    // ==========================================
    Route::prefix('add-ons')->name('user.add-ons.')->group(function () {
        // Browse add-ons
        Route::get('/', [\App\Http\Controllers\User\AddOnController::class, 'index'])->name('index');
        
        // My purchased add-ons
        Route::get('/my-add-ons', [\App\Http\Controllers\User\AddOnController::class, 'myAddOns'])->name('my-add-ons');
        
        // View specific add-on
        Route::get('/{addOn}', [\App\Http\Controllers\User\AddOnController::class, 'show'])->name('show');
        
        // Checkout
        Route::get('/{addOn}/checkout', [\App\Http\Controllers\User\AddOnController::class, 'checkout'])->name('checkout');
        Route::post('/{addOn}/purchase', [\App\Http\Controllers\User\AddOnController::class, 'purchase'])->name('purchase');
        
        // Payment processing
        Route::get('/payment/{userAddOn}/stripe', [\App\Http\Controllers\User\AddOnController::class, 'stripeCheckout'])->name('stripe-checkout');
        Route::get('/payment/{userAddOn}/success', [\App\Http\Controllers\User\AddOnController::class, 'paymentSuccess'])->name('payment-success');
        
        // Access purchased content
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
    // Resume Template Management Routes
    // ==========================================
    Route::resource('templates', TemplateController::class)->except(['show']);
    Route::get('templates/{id}/preview', [TemplateController::class, 'preview'])->name('templates.preview');
    Route::post('templates/{id}/preview-live', [TemplateController::class, 'previewLive'])->name('templates.preview-live');
    Route::post('templates/{id}/toggle-active', [TemplateController::class, 'toggleActive'])->name('templates.toggle-active');
    Route::post('templates/{id}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');

    // ==========================================
    // User Management Routes
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
});

/*
|--------------------------------------------------------------------------
| Test/Debug Routes (Remove in production)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/test-starter-templates', function() {
        return response()->json([
            'count' => \App\Models\Template::count(),
            'templates' => \App\Models\Template::take(3)->get(['id', 'name', 'category'])
        ]);
    });
}

/**
 * TEMPORARY DEBUG ROUTE
 * Add this to your routes/web.php file for debugging
 * Remove after fixing the issue!
 */

// Add this inside your admin routes group
Route::get('/templates/{id}/debug', function($id) {
    $template = \App\Models\Template::findOrFail($id);
    
    // Check for common issues
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
        if (strpos($template->css_content, 'color: white') !== false || 
            strpos($template->css_content, 'color:#fff') !== false) {
            $warnings[] = '⚠️ CSS uses white text - might be invisible on white background';
        }
    }
    
    // Find placeholders
    $placeholders = [];
    if (!empty($template->html_content)) {
        preg_match_all('/\{\{([^}]+)\}\}/', $template->html_content, $matches);
        $placeholders = array_unique($matches[1]);
    }
    
    // Build diagnostic output
    $output = '<!DOCTYPE html>
<html>
<head>
    <title>Template Debug - ' . htmlspecialchars($template->name) . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 30px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
            margin: 20px 0;
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #7f8c8d;
        }
        .issue {
            background: #e74c3c;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .warning {
            background: #f39c12;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #27ae60;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: "Courier New", monospace;
            font-size: 14px;
            margin: 10px 0;
            max-height: 300px;
            overflow-y: auto;
        }
        .placeholder-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }
        .placeholder-tag {
            background: #3498db;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Template Diagnostics</h1>
        
        <div class="info-grid">
            <div class="info-label">Template Name:</div>
            <div>' . htmlspecialchars($template->name) . '</div>
            
            <div class="info-label">Template ID:</div>
            <div>' . $template->id . '</div>
            
            <div class="info-label">Category:</div>
            <div>' . htmlspecialchars($template->category) . '</div>
            
            <div class="info-label">Status:</div>
            <div>' . ($template->is_active ? '✅ Active' : '❌ Inactive') . '</div>
            
            <div class="info-label">Premium:</div>
            <div>' . ($template->is_premium ? '⭐ Yes' : 'No') . '</div>
            
            <div class="info-label">Created:</div>
            <div>' . $template->created_at->format('Y-m-d H:i:s') . '</div>
        </div>';
    
    // Show issues
    if (!empty($issues)) {
        foreach ($issues as $issue) {
            $output .= '<div class="issue">🚨 ' . $issue . '</div>';
        }
    }
    
    // Show warnings
    if (!empty($warnings)) {
        foreach ($warnings as $warning) {
            $output .= '<div class="warning">' . $warning . '</div>';
        }
    }
    
    // Show success if no issues
    if (empty($issues) && empty($warnings)) {
        $output .= '<div class="success">✅ No obvious issues detected!</div>';
    }
    
    // Content lengths
    $output .= '<h2>📊 Content Statistics</h2>
        <div class="info-grid">
            <div class="info-label">HTML Length:</div>
            <div>' . strlen($template->html_content ?? '') . ' characters</div>
            
            <div class="info-label">CSS Length:</div>
            <div>' . strlen($template->css_content ?? '') . ' characters</div>
            
            <div class="info-label">Description Length:</div>
            <div>' . strlen($template->description ?? '') . ' characters</div>
        </div>';
    
    // Show placeholders
    if (!empty($placeholders)) {
        $output .= '<h2>🏷️ Placeholders Found (' . count($placeholders) . ')</h2>
            <div class="placeholder-list">';
        foreach ($placeholders as $placeholder) {
            $output .= '<span class="placeholder-tag">{{' . htmlspecialchars(trim($placeholder)) . '}}</span>';
        }
        $output .= '</div>';
    } else {
        $output .= '<h2>🏷️ Placeholders</h2>
            <div class="warning">⚠️ No placeholders found! Preview will show static content only.</div>';
    }
    
    // Show HTML preview
    $output .= '<h2>📝 HTML Content Preview</h2>';
    if (!empty($template->html_content)) {
        $htmlPreview = strlen($template->html_content) > 1000 
            ? substr($template->html_content, 0, 1000) . '...' 
            : $template->html_content;
        $output .= '<div class="code-block">' . htmlspecialchars($htmlPreview) . '</div>';
    } else {
        $output .= '<div class="issue">❌ No HTML content!</div>';
    }
    
    // Show CSS preview
    $output .= '<h2>🎨 CSS Content Preview</h2>';
    if (!empty($template->css_content)) {
        $cssPreview = strlen($template->css_content) > 1000 
            ? substr($template->css_content, 0, 1000) . '...' 
            : $template->css_content;
        $output .= '<div class="code-block">' . htmlspecialchars($cssPreview) . '</div>';
    } else {
        $output .= '<div class="warning">⚠️ No CSS content!</div>';
    }
    
    // Action buttons
    $output .= '<h2>🔧 Actions</h2>
        <div>
            <a href="' . route('admin.templates.preview', $template->id) . '" target="_blank" class="btn">
                👁️ View Preview
            </a>
            <a href="' . route('admin.templates.edit', $template->id) . '" class="btn">
                ✏️ Edit Template
            </a>
            <a href="' . route('admin.templates.index') . '" class="btn">
                📋 Back to Templates
            </a>
        </div>';
    
    $output .= '</div>
</body>
</html>';
    
    return response($output)->header('Content-Type', 'text/html');
})->name('admin.templates.debug');


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';