#!/usr/bin/env php
<?php

/**
 * Interview Prep File Path Verification Script
 *
 * This script helps verify that uploaded resume files are being
 * stored and retrieved correctly.
 *
 * Usage: php artisan tinker
 * Then: require 'verify-interview-prep.php'
 */

use App\Models\User;
use App\Services\JobMatchService;
use Illuminate\Support\Facades\Storage;

echo "=================================\n";
echo "Interview Prep File Path Verification\n";
echo "=================================\n\n";

// Check if storage directories exist
$storageRoot = storage_path('app');
$privateDir = storage_path('app/private');
$uploadsDir = storage_path('app/private/uploads');
$tempDir = storage_path('app/private/uploads/temp');

echo "Storage Directory Structure:\n";
echo "✓ Root: " . (is_dir($storageRoot) ? "EXISTS" : "MISSING") . " ($storageRoot)\n";
echo "✓ Private: " . (is_dir($privateDir) ? "EXISTS" : "MISSING") . " ($privateDir)\n";
echo "✓ Uploads: " . (is_dir($uploadsDir) ? "EXISTS" : "MISSING") . " ($uploadsDir)\n";
echo "✓ Temp: " . (is_dir($tempDir) ? "EXISTS" : "MISSING") . " ($tempDir)\n\n";

// List existing temp uploads
echo "Existing Temp Resume Files:\n";
if (Storage::disk('local')->exists('private/uploads/temp')) {
    $files = Storage::disk('local')->allFiles('private/uploads/temp');
    if (empty($files)) {
        echo "(No files found)\n";
    } else {
        foreach ($files as $file) {
            echo "  - " . $file . "\n";
        }
    }
} else {
    echo "(Directory doesn't exist yet)\n";
}
echo "\n";

// Test JobMatchService resolution
echo "Testing JobMatchService Resolution:\n";
$jobMatchService = app(JobMatchService::class);

$testPath = 'uploads/temp/1/test_resume.pdf';
echo "Test Path: $testPath\n";
echo "Full Private Path: " . storage_path('app/private/' . ltrim($testPath, '/')) . "\n";
echo "Full Public Path: " . storage_path('app/' . ltrim($testPath, '/')) . "\n\n";

// Test with real file if exists
$firstUser = User::first();
if ($firstUser) {
    echo "Testing with real user:\n";
    echo "User ID: " . $firstUser->id . "\n";

    $userTempDir = "private/uploads/temp/{$firstUser->id}";
    if (Storage::disk('local')->exists($userTempDir)) {
        $userFiles = Storage::disk('local')->files($userTempDir);
        if (!empty($userFiles)) {
            echo "User has " . count($userFiles) . " temp files\n";
            $relPath = str_replace('private/', '', $userFiles[0]);
            echo "First file relative path: $relPath\n";

            $result = $jobMatchService->analyzeUploadedResume($relPath);
            echo "Analysis result keys: " . implode(', ', array_keys($result)) . "\n";
            if (isset($result['raw_text'])) {
                echo "Raw text extracted: " . strlen($result['raw_text']) . " characters\n";
            }
        } else {
            echo "No temp files for this user\n";
        }
    } else {
        echo "No temp directory for this user\n";
    }
} else {
    echo "No users found in database\n";
}

echo "\n=================================\n";
echo "Verification Complete\n";
echo "=================================\n";
