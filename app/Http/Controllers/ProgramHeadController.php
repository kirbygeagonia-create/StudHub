<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Actions\LogAudit;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\Feedback;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProgramHeadController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $collegeId = $user->college_id;

        // All programs under this college (for resource counts etc.)
        $programIds = Program::where('college_id', $collegeId)->pluck('id');

        $openReports = Report::where('status', ReportStatus::Open->value)
            ->where('school_id', $user->school_id)
            ->count();

        $totalModerators = User::where('role', UserRole::Moderator->value)
            ->where('college_id', $collegeId)
            ->count();

        $activeUsers = User::whereNotNull('onboarded_at')
            ->whereNull('suspended_until')
            ->where('college_id', $collegeId)
            ->count();

        $totalResources = LearningResource::whereNull('deleted_at')
            ->whereIn('program_id', $programIds)
            ->count();

        $moderators = ProgramModerator::with([
            'user:id,display_name,name,program_id',
            'program:id,code,name',
        ])
            ->whereIn('program_id', $programIds)
            ->latest()
            ->paginate(50);

        $unreadFeedback = Feedback::where('recipient_role', 'program_head')
            ->where('recipient_college_id', $collegeId)
            ->whereNull('read_at')
            ->count();

        return view('program-head.dashboard', [
            'openReports' => $openReports,
            'totalModerators' => $totalModerators,
            'activeUsers' => $activeUsers,
            'totalResources' => $totalResources,
            'moderators' => $moderators,
            'unreadFeedback' => $unreadFeedback,
        ]);
    }

    public function assignModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $userId = (int) $validated['user_id'];
        $programId = (int) $validated['program_id'];

        // Ensure the program belongs to this Program Head's college
        $program = Program::findOrFail($programId);
        if ($program->college_id !== $user->college_id) {
            return redirect()->back()->withErrors([
                'error' => 'You can only assign moderators to programs within your college.',
            ]);
        }

        DB::transaction(function () use ($userId, $programId, $user): void {
            ProgramModerator::firstOrCreate(
                ['user_id' => $userId, 'program_id' => $programId],
                ['assigned_by_user_id' => $user->id]
            );

            User::where('id', $userId)->update(['role' => UserRole::Moderator]);
        });

        $logAudit->handle($user, 'moderator.assign', 'User', $userId, ['program_id' => $programId]);

        session()->flash('status', 'Moderator assigned to program.');

        return redirect()->route('program_head.dashboard');
    }

    public function removeModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'moderator_id' => ['required', 'integer', 'exists:program_moderators,id'],
        ]);

        $moderator = ProgramModerator::findOrFail((int) $validated['moderator_id']);

        $program = Program::findOrFail($moderator->program_id);

        if ($program->college_id !== $user->college_id) {
            return redirect()->back()->withErrors([
                'error' => 'You can only remove moderators from programs within your college.',
            ]);
        }

        DB::transaction(function () use ($moderator, &$userId): void {
            $userId = $moderator->user_id;
            $moderator->delete();

            if (! ProgramModerator::where('user_id', $userId)->exists()) {
                User::where('id', $userId)
                    ->where('role', UserRole::Moderator)
                    ->update(['role' => UserRole::Student]);
            }
        });

        $logAudit->handle($user, 'moderator.remove', 'User', $userId, []);

        session()->flash('status', 'Moderator removed from program.');

        return redirect()->route('program_head.dashboard');
    }

    public function suspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'days' => ['required', 'integer', 'min:1', 'max:365'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $target = User::findOrFail((int) $validated['user_id']);

        if ($target->college_id !== $user->college_id) {
            return redirect()->back()->withErrors([
                'error' => 'You can only suspend users within your college.',
            ]);
        }

        try {
            $suspendUser->handle($user, $target, (int) $validated['days'], $validated['reason'] ?? null);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'User suspended.');

        return redirect()->route('program_head.dashboard');
    }

    public function unsuspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $target = User::findOrFail((int) $validated['user_id']);

        if ($target->college_id !== $user->college_id) {
            return redirect()->back()->withErrors([
                'error' => 'You can only unsuspend users within your college.',
            ]);
        }

        try {
            $suspendUser->unsuspend($user, $target);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'User unsuspended.');

        return redirect()->route('program_head.dashboard');
    }

    public function feedback(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        // Mark all unread feedback for this college as read
        Feedback::where('recipient_role', 'program_head')
            ->where('recipient_college_id', $user->college_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $feedbacks = Feedback::with('user:id,display_name,name,email,program_id')
            ->where('recipient_role', 'program_head')
            ->where('recipient_college_id', $user->college_id)
            ->latest()
            ->paginate(25);

        return view('program-head.feedback', ['feedbacks' => $feedbacks]);
    }

    public function resolveFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        if ($feedback->recipient_college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This feedback does not belong to your college.']);
        }

        $feedback->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_note' => $httpRequest->input('resolution_note'),
        ]);

        session()->flash('status', 'Feedback resolved.');

        return redirect()->route('program_head.feedback');
    }

    public function escalateFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        if ($feedback->recipient_college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This feedback does not belong to your college.']);
        }

        DB::transaction(function () use ($feedback, $user): void {
            $feedback->update(['status' => 'escalated']);

            Feedback::create([
                'user_id' => $feedback->user_id,
                'type' => $feedback->type,
                'body' => $feedback->body,
                'recipient_role' => 'dean',
                'recipient_college_id' => $user->college_id,
                'recipient_program_id' => null,
                'escalated_from_id' => $feedback->id,
                'status' => 'open',
            ]);
        });

        session()->flash('status', 'Feedback escalated to Dean.');

        return redirect()->route('program_head.feedback');
    }
}
