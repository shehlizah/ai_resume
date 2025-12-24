#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set locale to 'en'
app()->setLocale('en');

echo "=== TESTING AutoTranslateResponse MIDDLEWARE ===\n\n";

// Create a test HTML response
$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Test Translate</title>
    <script>console.log('test');</script>
</head>
<body>
    <h1>Selamat datang</h1>
    <p>Ini adalah halaman uji terjemahan otomatis.</p>
    <div>
        <span>Terima kasih telah mengunjungi situs kami.</span>
    </div>
</body>
</html>
HTML;

echo "Original HTML:\n";
echo $html . "\n\n";

// Manually apply the middleware logic
$middleware = new \App\Http\Middleware\AutoTranslateResponse(
    app(\App\Services\GoogleTranslateService::class)
);

// Create a fake response object
$response = new \Illuminate\Http\Response($html);
$response->headers->set('Content-Type', 'text/html; charset=utf-8');

// Create a fake request
$request = \Illuminate\Http\Request::create('/test-html-translate?lang=en', 'GET');

// Apply middleware
$result = $middleware->handle($request, function () use ($response) {
    return $response;
});

echo "\n\nTranslated HTML:\n";
$translated = $result->getContent();
echo $translated . "\n\n";

echo "\n=== COMPARISON ===\n";
echo "Original  : " . (strpos($html, 'Selamat')      ? '✓ Has "Selamat"' : '✗ Missing "Selamat"') . "\n";
echo "Translated: " . (strpos($translated, 'Selamat') ? '✗ Still has "Selamat"' : '✓ Removed "Selamat"') . "\n";
echo "Translated: " . (strpos($translated, 'Welcome') ? '✓ Has "Welcome"' : '✗ Missing "Welcome"') . "\n";

echo "\n=== HEADERS ===\n";
foreach ($result->headers as $name => $value) {
    if (stripos($name, 'translate') !== false || stripos($name, 'locale') !== false) {
        echo "$name: " . implode(', ', (array)$value) . "\n";
    }
}
