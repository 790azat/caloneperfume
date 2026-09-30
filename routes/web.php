<?php

use App\Http\Controllers\BlobUploadController;
use App\Http\Controllers\SiteController;
use App\Livewire\Account;
use App\Livewire\Admin;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/works', [SiteController::class, 'works'])->name('works.index');
Route::get('/works/{work}', [SiteController::class, 'work'])->name('works.show');
Route::get('/videos', [SiteController::class, 'videos'])->name('videos');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/lang/{locale}', [SiteController::class, 'locale'])->name('locale');

Route::post('/import/blob-token', BlobUploadController::class)
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
    ->middleware('throttle:120,1');

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/account', Account::class)->name('account');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/blob-upload', BlobUploadController::class)->name('blob-upload');
    Route::livewire('/', Admin\Dashboard::class)->name('dashboard');
    Route::livewire('/works', Admin\Works::class)->name('works');
    Route::livewire('/works/create', Admin\WorkForm::class)->name('works.create');
    Route::livewire('/works/{work}/edit', Admin\WorkForm::class)->name('works.edit');
    Route::livewire('/import', Admin\Import::class)->name('import');
    Route::livewire('/categories', Admin\Categories::class)->name('categories');
    Route::livewire('/services', Admin\Services::class)->name('services');
    Route::livewire('/testimonials', Admin\Testimonials::class)->name('testimonials');
    Route::livewire('/inquiries', Admin\Inquiries::class)->name('inquiries');
    Route::livewire('/users', Admin\Users::class)->name('users');
    Route::livewire('/settings', Admin\Settings::class)->name('settings');
});
