protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'is_admin' => \App\Http\Middleware\IsAdmin::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'subscription' => \App\Http\Middleware\CheckSubscription::class,
    'package.check' => \App\Http\Middleware\CheckActivePackage::class,
    'locale' => \App\Http\Middleware\SetLocale::class,
];
