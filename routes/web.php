<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FooterItemController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\HomepageSectionItemController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\NavigationItemController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PageSectionController as AdminPageSectionController;
use App\Http\Controllers\Admin\PageSectionItemController as AdminPageSectionItemController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

foreach (['apps', 'for-parents', 'for-teachers', 'about-us', 'privacy-policy', 'data-deletion', 'contact-us', 'support'] as $slug) {
    Route::get('/'.$slug, [PageController::class, 'show'])->defaults('slug', $slug)->name('pages.'.$slug);
}

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('admin')->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('site-settings', SiteSettingController::class)->except(['show']);
        Route::resource('media-assets', MediaAssetController::class)->except(['show']);
        Route::resource('navigation-items', NavigationItemController::class)->except(['show']);
        Route::resource('footer-items', FooterItemController::class)->except(['show']);
        Route::resource('homepage-sections', HomepageSectionController::class)->except(['show']);
        Route::resource('homepage-sections.items', HomepageSectionItemController::class)->parameters(['items' => 'item'])->except(['show']);
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('pages.sections', AdminPageSectionController::class)->parameters(['sections' => 'section'])->except(['show']);
        Route::resource('pages.sections.items', AdminPageSectionItemController::class)->parameters(['sections' => 'section', 'items' => 'item'])->except(['show']);
    });
});
