<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

require __DIR__.'/webhook.php';
require __DIR__.'/well-known.php';
require __DIR__.'/auth.php';
require __DIR__.'/app.php';

Route::view('terms', 'legal.terms')->name('legal.terms');
Route::view('privacy', 'legal.privacy')->name('legal.privacy');
Route::view('data-deletion', 'legal.data-deletion')->name('legal.data-deletion');
