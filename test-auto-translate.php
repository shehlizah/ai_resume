<?php
// Test script to verify AutoTranslateResponse middleware

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$container = $app->make(\Illuminate\Contracts\Container\Container::class);
$kernel = $container->make(\Illuminate\Contracts\Http\Kernel::class);

// Create a test request for a page in English
$request = \Illuminate\Http\Request::create('/?lang=en', 'GET');

// Create a simple HTML response
$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
    <script>console.log('test');</script>
</head>
<body>
    <h1>Selamat datang</h1>
    <p>Ini adalah halaman uji</p>
    <div>Terima kasih telah berkunjung</div>
</body>
</html>
HTML;

// Create response
$response = new \Illuminate\Http\Response($html);
$response->headers->set('Content-Type', 'text/html; charset=UTF-8');

// Test the middleware directly
$middleware = new \App\Http\Middleware\AutoTranslateResponse(
    new \App\Services\GoogleTranslateService()
);

echo "Original HTML:\n";
echo substr($html, 0, 200) . "\n\n";

// Simulate middleware execution
$result = $middleware->handle(
    $request,
    function() use ($response) {
        return $response;
    }
);

echo "Translated HTML:\n";
echo substr($result->getContent(), 0, 200) . "\n";

// Check headers
echo "\n\nHeaders:\n";
foreach ($result->headers as $k => $v) {
    if (strpos($k, 'translate') !== false || strpos($k, 'Translate') !== false) {
        echo "$k: " . $v[0] . "\n";
    }
}
