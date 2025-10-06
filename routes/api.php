<?php

use App\Http\Controllers\ClickController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;


Route::get('/websites/{website}/today-clicks', [WebsiteController::class, 'todayClicks']);
Route::get('/websites/{website}/week-clicks', [WebsiteController::class, 'weekClicks']);
Route::get('/websites/{website}/recent-clicks', [WebsiteController::class, 'recentClicks']);
Route::get('/websites/{website}/clicks', [ClickController::class, 'getClickData']);
Route::get('/websites/{website}/hourly-stats', [ClickController::class, 'getHourlyStats']);

Route::post('/clicks', [ClickController::class, 'store']);
