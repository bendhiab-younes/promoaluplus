<?php

use App\Http\Controllers\ChatbotController;
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

// SEO — sitemap & robots (served dynamically so URLs match the active domain)
Route::get('/sitemap.xml', function () {
    $pages = ['home', 'services', 'portfolio', 'about', 'contact'];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($pages as $page) {
        $xml .= '  <url><loc>'.e(route($page)).'</loc><changefreq>weekly</changefreq></url>'."\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    $body = "User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n";

    return response($body, 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Quote submission
Route::post('/quote', [QuoteController::class, 'store'])
    ->middleware('throttle:12,1')
    ->name('quote.store');

// Chatbot API
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/welcome', [ChatbotController::class, 'getWelcome'])->name('welcome');
    Route::post('/message', [ChatbotController::class, 'getResponse'])->name('message');
    Route::get('/faqs', [ChatbotController::class, 'getFaqs'])->name('faqs');
});

// Document generation (admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/quote/{quote}/pdf', [\App\Http\Controllers\QuoteDocumentController::class, 'pdf'])->name('quote.pdf');
    Route::get('/quote/{quote}/excel', [\App\Http\Controllers\QuoteDocumentController::class, 'excel'])->name('quote.excel');
    Route::get('/invoice/{invoice}/pdf', [\App\Http\Controllers\PdfController::class, 'invoice'])->name('invoice.pdf');
});
