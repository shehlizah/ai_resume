#!/usr/bin/env php
<?php

echo "Testing Google Translate API directly...\n\n";

// Test data
$texts = [
    'Selamat datang',
    'Welcome',
    'Ini adalah halaman uji',
    'This is a test page',
];

foreach ($texts as $text) {
    $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
        'client' => 'gtx',
        'sl' => 'auto',
        'tl' => 'en',
        'dt' => 't',
        'q' => $text,
    ]);

    echo "Input: $text\n";
    echo "URL: " . substr($url, 0, 80) . "...\n";

    // Use cURL if available
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "ERROR: $error\n";
        } else {
            $json = json_decode($response, true);
            if ($json && isset($json[0][0][0])) {
                echo "Output: " . $json[0][0][0] . "\n";
            } else {
                echo "ERROR: No translation in response\n";
                echo "Response: " . substr($response, 0, 100) . "\n";
            }
        }
    } else {
        // Fallback to file_get_contents
        $response = @file_get_contents($url);
        if ($response === false) {
            echo "ERROR: Could not fetch URL\n";
        } else {
            $json = json_decode($response, true);
            if ($json && isset($json[0][0][0])) {
                echo "Output: " . $json[0][0][0] . "\n";
            } else {
                echo "ERROR: No translation in response\n";
            }
        }
    }
    echo "\n";
}
