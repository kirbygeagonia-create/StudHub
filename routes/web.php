<?php

use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LendController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramHeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SaoController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/help', fn () => redirect('/?open=help'))->name('help');
Route::get('/aup', fn () => redirect('/?open=aup'))->name('aup');

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

Route::middleware(['auth', 'verified', 'onboarded', 'role:moderator,program_head,dean,sao,super_admin'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'dashboard'])->name('moderation.dashboard');
    Route::get('/moderation/users/search', [ModerationController::class, 'userSearch'])
        ->name('moderation.users.search');
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

// Program Head routes (replaces old admin routes)
Route::middleware(['auth', 'verified', 'onboarded', 'role:program_head,dean,sao,super_admin'])->group(function () {
    Route::get('/program-head', [ProgramHeadController::class, 'dashboard'])->name('program_head.dashboard');
    Route::post('/program-head/moderators/assign', [ProgramHeadController::class, 'assignModerator'])
        ->middleware('throttle:20,1')
        ->name('program_head.moderators.assign');
    Route::post('/program-head/moderators/remove', [ProgramHeadController::class, 'removeModerator'])
        ->middleware('throttle:20,1')
        ->name('program_head.moderators.remove');
    Route::post('/program-head/suspend', [ProgramHeadController::class, 'suspend'])
        ->middleware('throttle:10,1')
        ->name('program_head.suspend');
    Route::post('/program-head/unsuspend', [ProgramHeadController::class, 'unsuspend'])
        ->middleware('throttle:10,1')
        ->name('program_head.unsuspend');
    Route::get('/program-head/feedback', [ProgramHeadController::class, 'feedback'])->name('program_head.feedback');
    Route::post('/program-head/feedback/{feedback}/resolve', [ProgramHeadController::class, 'resolveFeedback'])
        ->middleware('throttle:20,1')
        ->name('program_head.feedback.resolve');
    Route::post('/program-head/feedback/{feedback}/escalate', [ProgramHeadController::class, 'escalateFeedback'])
        ->middleware('throttle:20,1')
        ->name('program_head.feedback.escalate');
});

// Dean routes
Route::middleware(['auth', 'verified', 'onboarded', 'role:dean,sao,super_admin'])->group(function () {
    Route::get('/dean', [DeanController::class, 'dashboard'])->name('dean.dashboard');
    Route::get('/dean/feedback', [DeanController::class, 'feedback'])->name('dean.feedback');
    Route::post('/dean/feedback/{feedback}/resolve', [DeanController::class, 'resolveFeedback'])
        ->middleware('throttle:20,1')
        ->name('dean.feedback.resolve');
    Route::post('/dean/feedback/{feedback}/escalate', [DeanController::class, 'escalateFeedback'])
        ->middleware('throttle:20,1')
        ->name('dean.feedback.escalate');
    Route::get('/dean/programs', [DeanController::class, 'programs'])->name('dean.programs');
    Route::post('/dean/program-heads/assign', [DeanController::class, 'assignProgramHead'])
        ->middleware('throttle:20,1')
        ->name('dean.program_heads.assign');
});

// SAO routes (highest school-side authority)
Route::middleware(['auth', 'verified', 'onboarded', 'role:sao,super_admin'])->group(function () {
    Route::get('/sao', [SaoController::class, 'dashboard'])->name('sao.dashboard');
    Route::get('/sao/feedback', [SaoController::class, 'feedback'])->name('sao.feedback');
    Route::post('/sao/feedback/{feedback}/resolve', [SaoController::class, 'resolveFeedback'])
        ->middleware('throttle:20,1')
        ->name('sao.feedback.resolve');
    Route::get('/sao/users', [SaoController::class, 'users'])->name('sao.users');
    Route::post('/sao/users/assign-role', [SaoController::class, 'assignRole'])
        ->middleware('throttle:20,1')
        ->name('sao.users.assign-role');
    Route::get('/sao/announcements', [SaoController::class, 'announcements'])->name('sao.announcements');
});

// Legacy /admin routes — redirect to new program_head routes for backwards compat
Route::middleware(['auth', 'verified', 'onboarded'])->group(function () {
    Route::redirect('/admin', '/program-head')->name('admin.dashboard');
    Route::redirect('/admin/feedback', '/program-head/feedback')->name('admin.feedback');
    Route::redirect('/admin/super', '/sao')->name('admin.super');
});

require __DIR__ . '/auth.php';
