<?php

namespace App\Providers;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\ChatMessage;
use App\Models\Feedback;
use App\Models\LearningResource;
use App\Models\Lend;
use App\Models\Offer;
use App\Models\Report;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'resource' => LearningResource::class,
            'message' => ChatMessage::class,
            'user' => User::class,
            'subject' => Subject::class,
            'report' => Report::class,
            'offer' => Offer::class,
            'lend' => Lend::class,
        ]);

        // Share admin sidebar badge counts via View Composer
        // so every admin controller method doesn't re-query them.
        View::composer([
            'sao._sidebar', 'sao.dashboard', 'sao.feedback', 'sao.users', 'sao.announcements',
            'dean._sidebar', 'dean.dashboard', 'dean.feedback', 'dean.programs',
            'program-head._sidebar', 'program-head.dashboard',
        ], function ($view): void {
            $user = Auth::user();
            if ($user === null) {
                return;
            }

            $role = $user->role instanceof UserRole ? $user->role->value : null;

            // Open reports — same scope for all admin roles
            $openReports = Report::where('status', ReportStatus::Open->value)
                ->where('school_id', $user->school_id)
                ->count();

            // Unread feedback — scoped by role
            $unreadFeedback = match ($role) {
                'sao', 'super_admin' => Feedback::whereIn('recipient_role', ['sao', 'super_admin'])
                    ->whereNull('read_at')
                    ->count(),
                'dean' => Feedback::where('recipient_role', 'dean')
                    ->where('recipient_college_id', $user->college_id)
                    ->whereNull('read_at')
                    ->count(),
                'program_head' => Feedback::where('recipient_role', 'program_head')
                    ->where('recipient_college_id', $user->college_id)
                    ->whereNull('read_at')
                    ->count(),
                default => 0,
            };

            $view->with('openReports', $openReports);
            $view->with('unreadFeedback', $unreadFeedback);
        });
    }
}
