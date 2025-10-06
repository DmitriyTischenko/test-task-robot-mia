<?php

use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('websites.index');
});

Route::resource('websites', WebsiteController::class);

Route::get('/websites/{website}/analytics', [WebsiteController::class, 'show'])->name('websites.analytics');
