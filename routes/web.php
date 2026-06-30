<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LeadController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ProductController;
use App\Http\Controllers\Site\ServiceController;
use App\Http\Controllers\Site\SitemapController;
use App\Http\Controllers\Site\TeamController;
use App\Http\Controllers\Site\WorkController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/work', [WorkController::class, 'index'])->name('work.index');
Route::get('/work/{slug}', [WorkController::class, 'show'])->name('work.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/process', [PageController::class, 'process'])->name('process');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/start', [LeadController::class, 'create'])->name('start');
Route::post('/start', [LeadController::class, 'store'])->middleware('throttle:6,1')->name('leads.store');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// On-the-fly responsive image variants (WebP) for images on the public disk.
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('img');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// TEMP (local only) — visual preview of the error/maintenance/security designs. Remove after review.
if (app()->environment('local')) {
    Route::get('/__error-preview/{code}', function (string $code) {
        $codes = ['401', '403', '404', '419', '429', '500', '503', 'maintenance', 'security'];
        abort_unless(in_array($code, $codes, true), 404);
        $status = match ($code) { 'maintenance' => 503, 'security' => 403, default => (int) $code };

        return response()->view("errors.{$code}", [], $status);
    })->name('error.preview');
}
