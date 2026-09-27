<?php

use App\Http\Controllers\Admin\AdminAboutController;
use App\Http\Controllers\Admin\AdminCityController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminLocalityController;
use App\Http\Controllers\Admin\AdminMatchController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminRequirementController;
use App\Http\Controllers\Admin\AdminSeoController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminUpdateController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\LlmsController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Installer Routes (Pre-installation & Setup)
|--------------------------------------------------------------------------
*/
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::post('/verify-customer', [InstallController::class, 'verifyCustomer'])->name('verify');
    Route::post('/test-db', [InstallController::class, 'testDatabase'])->name('test_db');
    Route::post('/process', [InstallController::class, 'executeInstall'])->name('process');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}/{slug?}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{property}/inquiry', [PropertyController::class, 'storeInquiry'])->name('properties.inquiry');
Route::get('/api/cities/{city}/localities', [HomeController::class, 'getLocalities'])->name('api.cities.localities');

// SEO & AI Crawlers Discovery Endpoints
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/llms.txt', [LlmsController::class, 'index'])->name('llms.txt');
Route::get('/llms-full.txt', [LlmsController::class, 'full'])->name('llms.full');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/select-package', [DashboardController::class, 'selectPackage'])->name('dashboard.select_package');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');

    // Listings (Seller / Staff / Admin)
    Route::get('/listings', [PropertyController::class, 'myListings'])->name('listings.index');
    Route::get('/listings/create', [PropertyController::class, 'create'])->name('listings.create');
    Route::post('/listings', [PropertyController::class, 'store'])->name('listings.store');
    Route::get('/listings/{property}/edit', [PropertyController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{property}', [PropertyController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{property}', [PropertyController::class, 'destroy'])->name('listings.destroy');

    // Requirements (Buyer / Staff / Admin)
    Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index');
    Route::get('/requirements/create', [RequirementController::class, 'create'])->name('requirements.create');
    Route::post('/requirements', [RequirementController::class, 'store'])->name('requirements.store');
    Route::get('/requirements/{requirement}/edit', [RequirementController::class, 'edit'])->name('requirements.edit');
    Route::put('/requirements/{requirement}', [RequirementController::class, 'update'])->name('requirements.update');
    Route::delete('/requirements/{requirement}', [RequirementController::class, 'destroy'])->name('requirements.destroy');
    Route::get('/requirements/{requirement}/matches', [RequirementController::class, 'matches'])->name('requirements.matches');

    /*
    |--------------------------------------------------------------------------
    | Staff Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:STAFF,ADMIN')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/users', [StaffController::class, 'users'])->name('users');
        Route::get('/users/search', [StaffController::class, 'searchUsers'])->name('users.search');
        Route::get('/users/create', [StaffController::class, 'createUser'])->name('users.create');
        Route::post('/users', [StaffController::class, 'storeUser'])->name('users.store');
        Route::get('/matches', [StaffController::class, 'matches'])->name('matches');
        Route::post('/properties/{property}/approve', [AdminPropertyController::class, 'approve'])->name('properties.approve');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Cities & Showcase Images (Explore Popular Cities)
        Route::get('/cities', [AdminCityController::class, 'index'])->name('cities.index');
        Route::post('/cities', [AdminCityController::class, 'store'])->name('cities.store');
        Route::put('/cities/{city}', [AdminCityController::class, 'update'])->name('cities.update');
        Route::post('/cities/{city}/toggle-popular', [AdminCityController::class, 'togglePopular'])->name('cities.toggle-popular');
        Route::delete('/cities/{city}', [AdminCityController::class, 'destroy'])->name('cities.destroy');

        // Localities
        Route::get('/localities', [AdminLocalityController::class, 'index'])->name('localities.index');
        Route::post('/localities', [AdminLocalityController::class, 'store'])->name('localities.store');
        Route::put('/localities/{locality}', [AdminLocalityController::class, 'update'])->name('localities.update');
        Route::delete('/localities/{locality}', [AdminLocalityController::class, 'destroy'])->name('localities.destroy');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');

        // Staff
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [AdminStaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [AdminStaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/activity', [AdminStaffController::class, 'activity'])->name('staff.activity');

        // Packages
        Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');
        Route::post('/packages/{package}/toggle', [AdminPackageController::class, 'toggleStatus'])->name('packages.toggle');

        // Properties
        Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
        Route::post('/properties/{property}/status', [AdminPropertyController::class, 'updateStatus'])->name('properties.status');
        Route::post('/properties/{property}/approve', [AdminPropertyController::class, 'approve'])->name('properties.approve');
        Route::delete('/properties/{property}', [AdminPropertyController::class, 'destroy'])->name('properties.destroy');

        // Requirements
        Route::get('/requirements', [AdminRequirementController::class, 'index'])->name('requirements.index');
        Route::post('/requirements/{requirement}/status', [AdminRequirementController::class, 'updateStatus'])->name('requirements.status');
        Route::post('/requirements/{requirement}/rematch', [AdminRequirementController::class, 'reMatch'])->name('requirements.rematch');

        // Matches
        Route::get('/matches', [AdminMatchController::class, 'index'])->name('matches.index');
        Route::post('/matches/recalculate', [AdminMatchController::class, 'recalculateAll'])->name('matches.recalculate');

        // SEO Suite
        Route::resource('seo', AdminSeoController::class)->names('seo');

        // About Page Manager
        Route::get('/about', [AdminAboutController::class, 'edit'])->name('about.edit');
        Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');

        // Activity logs
        Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');

        // System Auto-Updates
        Route::get('/updates', [AdminUpdateController::class, 'index'])->name('updates.index');
        Route::get('/updates/check', [AdminUpdateController::class, 'check'])->name('updates.check');
        Route::post('/updates/apply', [AdminUpdateController::class, 'apply'])->name('updates.apply');
    });
});
