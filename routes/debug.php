<?php

use Illuminate\Support\Facades\Route;

// Debug route - test locale and translation
Route::get('/debug/locale', function () {
    return view('debug.locale');
})->name('debug.locale');
