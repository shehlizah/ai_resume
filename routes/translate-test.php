<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-translate', function () {
    $service = app(\App\Services\GoogleTranslateService::class);

    $testText = 'Selamat datang';
    $result = [];

    try {
        $translated = $service->translate($testText, 'en');
        $result['success'] = true;
        $result['input'] = $testText;
        $result['output'] = $translated;
        $result['locale'] = app()->getLocale();
    } catch (\Throwable $e) {
        $result['success'] = false;
        $result['error'] = $e->getMessage();
        $result['locale'] = app()->getLocale();
    }

    return response()->json($result);
});
