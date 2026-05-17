<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LendController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified', 'onboarded', 'not_suspended'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{room}', [ChatController::class, 'show'])->name('chat.show');

    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::post('/resources/{resource}/toggle-save', [ResourceController::class, 'toggleSave'])->name('resources.toggle-save');
    Route::get('/my-shelf', [ResourceController::class, 'shelf'])->name('resources.shelf');

    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{request}/offers', [RequestController::class, 'storeOffer'])->name('requests.offers.store');
    Route::post('/requests/{request}/offers/{offer}/accept', [RequestController::class, 'acceptOffer'])->name('requests.offers.accept');

    Route::get('/lends', [LendController::class, 'index'])->name('lends.index');
    Route::post('/requests/{request}/offers/{offer}/lend', [LendController::class, 'record'])->name('lends.record');
    Route::post('/lends/{lend}/return', [LendController::class, 'return'])->name('lends.return');

    Route::get('/leaderboard', [ProfileController::class, 'leaderboard'])->name('leaderboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/users/{user}', [ProfileController::class, 'publicProfile'])->name('profile.public');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

Route::middleware(['auth', 'verified', 'onboarded', 'role:moderator,admin'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'dashboard'])->name('moderation.dashboard');
    Route::post('/moderation/reports/{report}/resolve', [ModerationController::class, 'resolve'])->name('moderation.resolve');
    Route::post('/moderation/suspend', [ModerationController::class, 'suspend'])->name('moderation.suspend');
    Route::post('/moderation/unsuspend', [ModerationController::class, 'unsuspend'])->name('moderation.unsuspend');
});

Route::middleware(['auth', 'verified', 'onboarded', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/moderators/assign', [AdminController::class, 'assignModerator'])->name('admin.moderators.assign');
    Route::post('/admin/moderators/remove', [AdminController::class, 'removeModerator'])->name('admin.moderators.remove');
    Route::post('/admin/suspend', [AdminController::class, 'suspend'])->name('admin.suspend');
    Route::post('/admin/unsuspend', [AdminController::class, 'unsuspend'])->name('admin.unsuspend');
});

require __DIR__ . '/auth.php';