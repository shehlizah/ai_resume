#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set locale to 'en'
app()->setLocale('en');

// Test the service directly
$service = app(\App\Services\GoogleTranslateService::class);

$testInputs = [
    'Selamat datang',
    'Ini adalah halaman uji',
    'Terima kasih telah berkunjung',
    'Halo dunia'
];

echo "Testing GoogleTranslateService directly:\n";
echo str_repeat("=", 60) . "\n";

foreach ($testInputs as $text) {
    echo "\nInput: $text\n";
    $translated = $service->translate($text, 'en');
    echo "Output: $translated\n";
    echo "Same? " . ($text === $translated ? 'YES (NO TRANSLATION)' : 'NO (TRANSLATED)') . "\n";
}

echo "\n\n";
echo "Checking logs in storage/logs/:\n";
exec('ls -lh c:\\Users\\dell\\ai_resume\\storage\\logs\\ | tail -5', $files);
foreach ($files as $f) {
    echo $f . "\n";
}
