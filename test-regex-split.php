#!/usr/bin/env php
<?php

// Test regex split behavior
$html = '<h1>Selamat datang</h1><p>Test</p>';

echo "HTML: $html\n\n";

$parts = preg_split('/(>|<)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

echo "Split result (" . count($parts) . " parts):\n";
foreach ($parts as $i => $part) {
    $type = ($i % 2 === 1) ? 'DELIM' : 'TEXT';
    echo "[$i] ($type): '$part'\n";
}

echo "\n\nProcessing logic:\n";
$inTag = false;
foreach ($parts as $i => $part) {
    if ($i % 2 === 1) {
        echo "[$i] Delimiter '$part' → ";
        if ($part === '<') {
            $inTag = true;
            echo "enter tag\n";
        } else {
            $inTag = false;
            echo "exit tag\n";
        }
    } else {
        $status = $inTag ? 'inside tag' : 'outside tag (TRANSLATE)';
        echo "[$i] Text '$part' ($status)\n";
    }
}
