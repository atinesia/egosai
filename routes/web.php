<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard\AiSettings;
use App\Livewire\Dashboard\BroadcastManager;
use App\Livewire\Dashboard\Contacts;
use App\Livewire\Dashboard\Inbox;
use App\Livewire\Dashboard\KnowledgeBaseManager;
use App\Livewire\Dashboard\Onboarding;
use App\Livewire\Dashboard\QuickReplyManager;
use App\Livewire\Dashboard\Reports;
use App\Livewire\Dashboard\WhatsappManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Onboarding (tenant sudah ada, tapi belum selesai setup awal)
Route::middleware(['auth', 'ensure.tenant'])->group(function () {
    Route::get('/onboarding', Onboarding::class)->name('onboarding');
});

// Dashboard (perlu login + tenant aktif + sudah onboarding)
Route::middleware(['auth', 'ensure.tenant', 'ensure.onboarded'])->prefix('dashboard')->group(function () {
    Route::get('/inbox', Inbox::class)->name('dashboard.inbox');
    Route::get('/contacts', Contacts::class)->name('dashboard.contacts');
    Route::get('/reports', Reports::class)->name('dashboard.reports');
    Route::get('/knowledge-base', KnowledgeBaseManager::class)->name('dashboard.knowledge-base');
    Route::get('/ai-settings', AiSettings::class)->name('dashboard.ai-settings');
    Route::get('/whatsapp', WhatsappManager::class)->name('dashboard.whatsapp');
    Route::get('/quick-replies', QuickReplyManager::class)->name('dashboard.quick-replies');
    Route::get('/broadcast', BroadcastManager::class)->name('dashboard.broadcast');
});
