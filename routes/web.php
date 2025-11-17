<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\TemplateStarterController;
use App\Http\Controllers\UserResumeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\UserSubscriptionController as AdminUserSubscriptionController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\CoverLetterController;

/*
|--------------------------------------------------------------------------
| Admin Routes - Subscription Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Subscription Plans Management
    Route::resource('subscription-plans', SubscriptionPlanController::class);
    Route::post('subscription-plans/{subscriptionPlan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('subscription-plans.toggle-status');

    // User Subscriptions Management
    Route::prefix('user-subscriptions')->name('user-subscriptions.')->group(function () {
        Route::get('/', [AdminUserSubscriptionController::class, 'index'])->name('index');
        Route::get('/{userSubscription}', [AdminUserSubscriptionController::class, 'show'])->name('show');
        Route::post('/{userSubscription}/cancel', [AdminUserSubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{userSubscription}/reactivate', [AdminUserSubscriptionController::class, 'reactivate'])->name('reactivate');
        Route::post('/{userSubscription}/extend', [AdminUserSubscriptionController::class, 'extend'])->name('extend');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
        Route::get('/export/csv', [AdminPaymentController::class, 'export'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| User Routes - Subscription & Billing
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Pricing & Plans
    Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('user.pricing');

    // Subscription Management
    Route::prefix('subscription')->name('user.subscription.')->group(function () {
        Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/change-billing', [SubscriptionController::class, 'changeBillingPeriod'])->name('change-billing');
    });

    // Payment Processing
    Route::prefix('payment')->name('user.payment.')->group(function () {
        // Stripe
        Route::post('/stripe/checkout', [PaymentController::class, 'stripeCheckout'])->name('stripe.checkout');
        Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');

        // PayPal
        Route::post('/paypal/checkout', [PaymentController::class, 'paypalCheckout'])->name('paypal.checkout');
        Route::post('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
        Route::get('/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('paypal.cancel');
    });
});

/*
|--------------------------------------------------------------------------
| User Resume Routes
|--------------------------------------------------------------------------
| Add these routes to your routes/web.php file
*/

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    // Resume Management Routes
    Route::prefix('resumes')->name('resumes.')->group(function () {

        // List all resumes
        Route::get('/', [UserResumeController::class, 'index'])
            ->name('index');

        // Choose template
        Route::get('/choose', [UserResumeController::class, 'chooseTemplate'])
            ->name('choose');

        // Preview template with sample data
        Route::get('/preview/{template_id}', [UserResumeController::class, 'preview'])
            ->name('preview');

        // Fill form for specific template
        Route::get('/fill/{template_id}', [UserResumeController::class, 'fillForm'])
            ->name('fill');

        // Generate PDF (saves and redirects to success)
        Route::post('/generate', [UserResumeController::class, 'generate'])
            ->name('generate');

        // Success page (auto-opens PDF)
        Route::get('/success/{id}', [UserResumeController::class, 'success'])
            ->name('success');

        // View resume PDF in browser
        Route::get('/view/{id}', [UserResumeController::class, 'view'])
            ->name('view');

        // Download resume
        Route::get('/download/{id}', [UserResumeController::class, 'download'])
            ->name('download');

        // Delete resume
        Route::delete('/{id}', [UserResumeController::class, 'destroy'])
            ->name('destroy');
    });

    // Cover Letter Management Routes
    Route::prefix('cover-letters')->name('cover-letters.')->group(function () {

        // List all cover letters
        Route::get('/', [CoverLetterController::class, 'index'])
            ->name('index');

        // Create new cover letter form
        Route::get('/create', [CoverLetterController::class, 'create'])
            ->name('create');

        // Store cover letter
        Route::post('/', [CoverLetterController::class, 'store'])
            ->name('store');

        // View specific cover letter
        Route::get('/{coverLetter}', [CoverLetterController::class, 'view'])
            ->name('view');

        // Print cover letter
        Route::get('/{coverLetter}/print', [CoverLetterController::class, 'print'])
            ->name('print');

        // Edit cover letter form
        Route::get('/{coverLetter}/edit', [CoverLetterController::class, 'edit'])
            ->name('edit');

        // Update cover letter
        Route::put('/{coverLetter}', [CoverLetterController::class, 'update'])
            ->name('update');

        // Delete cover letter
        Route::delete('/{coverLetter}', [CoverLetterController::class, 'destroy'])
            ->name('destroy');
    });

});
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/



// Route::get('/', function () {
//     return view('welcome');
// })->name('home');



Route::get('/', function () {
    return view('frontend.pages.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Your other admin routes go here:
    // Route::resource('users', UserController::class);
    // Route::resource('templates', TemplateController::class);

});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
});



Route::middleware(['auth'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [UserDashboardController::class, 'getStats'])->name('dashboard.stats');
});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('templates', TemplateController::class);
    Route::get('templates/{template}/preview', [TemplateController::class, 'preview'])
        ->name('templates.preview');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Template CRUD Routes (using resource controller)
    Route::resource('templates', TemplateController::class)->except(['show']);

    // Additional Template Actions
    Route::get('templates/{id}/preview', [TemplateController::class, 'preview'])
        ->name('templates.preview');
    Route::post('templates/{id}/preview-live', [TemplateController::class, 'previewLive'])
        ->name('templates.preview-live');
    Route::post('templates/{id}/toggle-active', [TemplateController::class, 'toggleActive'])
        ->name('templates.toggle-active');
    Route::post('templates/{id}/duplicate', [TemplateController::class, 'duplicate'])
        ->name('templates.duplicate');



    // ==========================================
    // User Management Routes
    // ==========================================
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Toggle user status (activate/deactivate)
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    // Delete specific user resume
    Route::delete('/users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteResume'])
        ->name('users.delete-resume');

    // Download user resume
    Route::get('/users/{userId}/resumes/{resumeId}/download', [UserController::class, 'downloadResume'])
        ->name('users.download-resume');

    // Bulk actions (activate, deactivate, delete multiple users)
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])
        ->name('users.bulk-action');
});

/*
|--------------------------------------------------------------------------
| User Resume Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/resumes', [UserResumeController::class, 'index'])->name('user.resumes');
    Route::get('/resumes/create', [UserResumeController::class, 'chooseTemplate'])->name('user.resumes.choose');
    Route::get('/resumes/fill/{template}', [UserResumeController::class, 'fillForm'])->name('user.resumes.fill');
    Route::post('/resumes/generate', [UserResumeController::class, 'generate'])->name('user.resumes.generate');
});

/*
|--------------------------------------------------------------------------
| Test/Debug Routes (Remove in production)
|--------------------------------------------------------------------------
*/

Route::get('/test-starter-templates', function() {
    return response()->json([
        'count' => \App\Models\Template::count(),
        'templates' => \App\Models\Template::take(3)->get(['id', 'name', 'category'])
    ]);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
