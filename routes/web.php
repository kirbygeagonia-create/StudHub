<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LendController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/help', 'pages.help')->name('help');
Route::view('/aup', 'pages.aup')->name('aup');

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
    Route::get('/chat/attachments/{message}/download', [ChatAttachmentController::class, 'download'])
        ->middleware(['auth', 'verified'])
        ->name('chat.attachments.download');

    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])
        ->middleware('throttle:30,1')
        ->name('resources.download');
    Route::post('/resources/{resource}/toggle-save', [ResourceController::class, 'toggleSave'])
        ->middleware('throttle:30,1')
        ->name('resources.toggle-save');
    Route::post('/resources/{resource}/mark-helpful', [ResourceController::class, 'markHelpful'])
        ->middleware('throttle:30,1')
        ->name('resources.mark-helpful');
    Route::get('/my-shelf', [ResourceController::class, 'shelf'])->name('resources.shelf');

    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('requests.store');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{request}/offers', [RequestController::class, 'storeOffer'])
        ->middleware('throttle:10,1')
        ->name('requests.offers.store');
    Route::post('/requests/{request}/offers/{offer}/accept', [RequestController::class, 'acceptOffer'])
        ->middleware('throttle:10,1')
        ->name('requests.offers.accept');

    Route::get('/lends', [LendController::class, 'index'])->name('lends.index');
    Route::post('/requests/{request}/offers/{offer}/lend', [LendController::class, 'record'])
        ->middleware('throttle:10,1')
        ->name('lends.record');
    Route::post('/lends/{lend}/return', [LendController::class, 'return'])
        ->middleware('throttle:10,1')
        ->name('lends.return');

    Route::get('/leaderboard', [ProfileController::class, 'leaderboard'])->name('leaderboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/users/{user}', [ProfileController::class, 'publicProfile'])->name('profile.public');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('profile.update');
    Route::get('/notification-preferences', [ProfileController::class, 'notificationPreferences'])
        ->name('profile.notification-preferences');
    Route::post('/notification-preferences', [ProfileController::class, 'updateNotificationPreferences'])
        ->middleware('throttle:10,1')
        ->name('profile.notification-preferences.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('throttle:5,1')
        ->name('profile.destroy');

    Route::get('/search', [SearchController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('search');

    Route::post('/reports', [ReportController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reports.store');

    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('feedback.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware(['auth', 'verified', 'onboarded', 'role:moderator,admin,super_admin'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'dashboard'])->name('moderation.dashboard');
    Route::post('/moderation/reports/{report}/resolve', [ModerationController::class, 'resolve'])
        ->middleware('throttle:20,1')
        ->name('moderation.resolve');
    Route::post('/moderation/suspend', [ModerationController::class, 'suspend'])
        ->middleware('throttle:10,1')
        ->name('moderation.suspend');
    Route::post('/moderation/unsuspend', [ModerationController::class, 'unsuspend'])
        ->middleware('throttle:10,1')
        ->name('moderation.unsuspend');
});

Route::middleware(['auth', 'verified', 'onboarded', 'role:admin,super_admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/moderators/assign', [AdminController::class, 'assignModerator'])
        ->middleware('throttle:20,1')
        ->name('admin.moderators.assign');
    Route::post('/admin/moderators/remove', [AdminController::class, 'removeModerator'])
        ->middleware('throttle:20,1')
        ->name('admin.moderators.remove');
    Route::post('/admin/suspend', [AdminController::class, 'suspend'])
        ->middleware('throttle:10,1')
        ->name('admin.suspend');
    Route::post('/admin/unsuspend', [AdminController::class, 'unsuspend'])
        ->middleware('throttle:10,1')
        ->name('admin.unsuspend');
    Route::get('/admin/feedback', [AdminController::class, 'feedback'])->name('admin.feedback');
});

// SuperAdmin-only routes (system-level management)
Route::middleware(['auth', 'verified', 'onboarded', 'role:super_admin'])->group(function () {
    // SuperAdmin can manage all feedback and system settings
    Route::get('/admin/super', [AdminController::class, 'superDashboard'])->name('admin.super');
});

require __DIR__ . '/auth.php';
