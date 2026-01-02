<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/locale/{locale}', [PageController::class, 'setLocale'])->name('locale.set');

// Main pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Quote submission
Route::post('/quote', [QuoteController::class, 'store'])->name('quote.store');
