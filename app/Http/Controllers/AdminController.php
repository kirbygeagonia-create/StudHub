<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Actions\LogAudit;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\ChatMessage;
use App\Models\College;
use App\Models\LearningResource;
use App\Models\Lend;
use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\Report;
use App\Models\ResourceRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $openReports = Report::where('status', ReportStatus::Open->value)->count();
        $totalModerators = User::where('role', UserRole::Moderator->value)->count();
        $activeUsers = User::whereNotNull('onboarded_at')->whereNull('suspended_until')->count();
        $totalResources = LearningResource::whereNull('deleted_at')->count();
        $totalRequests = ResourceRequest::count();
        $activeLends = Lend::whereNull('returned_at')->count();
        $messagesToday = ChatMessage::whereDate('created_at', now()->toDateString())->count();

        $recentSignups = User::whereNotNull('onboarded_at')
            ->where('onboarded_at', '>=', now()->subDays(7))
            ->count();

        $dau = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->startOfDay())
            ->count();

        $programs = Program::with('college')->orderBy('code')->get(['id', 'code', 'name', 'college_id']);

        $moderators = ProgramModerator::with(['user:id,display_name,name,program_id', 'program:id,code,name'])
            ->latest()
            ->get();

        $collegeStats = College::withCount([
            'programs as program_count',
            'users as active_user_count' => function ($q): void {
                $q->whereNotNull('onboarded_at')->whereNull('suspended_until');
            },
        ])->where('school_id', $user->school_id)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $crossProgramFlows = DB::table('resources')
            ->join('users as owners', 'resources.owner_user_id', '=', 'owners.id')
            ->join('programs as owner_programs', 'owners.program_id', '=', 'owner_programs.id')
            ->join('subjects', 'resources.subject_id', '=', 'subjects.id')
            ->join('program_subjects', 'subjects.id', '=', 'program_subjects.subject_id')
            ->join('programs as target_programs', 'program_subjects.program_id', '=', 'target_programs.id')
            ->where('owner_programs.id', '!=', DB::raw('target_programs.id'))
            ->where('resources.school_id', $user->school_id)
            ->selectRaw('owner_programs.code as from_program, target_programs.code as to_program, COUNT(*) as count')
            ->groupBy('owner_programs.code', 'target_programs.code')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'openReports' => $openReports,
            'totalModerators' => $totalModerators,
            'activeUsers' => $activeUsers,
            'totalResources' => $totalResources,
            'totalRequests' => $totalRequests,
            'activeLends' => $activeLends,
            'messagesToday' => $messagesToday,
            'recentSignups' => $recentSignups,
            'dau' => $dau,
            'programs' => $programs,
            'moderators' => $moderators,
            'collegeStats' => $collegeStats,
            'crossProgramFlows' => $crossProgramFlows,
        ]);
    }

    public function assignModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $admin = $httpRequest->user();
        abort_unless($admin !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $userId = (int) $validated['user_id'];
        $programId = (int) $validated['program_id'];

        ProgramModerator::firstOrCreate(
            ['user_id' => $userId, 'program_id' => $programId],
            ['assigned_by_user_id' => $admin->id]
        );

        User::where('id', $userId)->update(['role' => UserRole::Moderator]);

        $logAudit->handle($admin, 'moderator.assign', 'User', $userId, ['program_id' => $programId]);

        session()->flash('status', 'Moderator assigned to program.');

        return redirect()->route('admin.dashboard');
    }

    public function removeModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $admin = $httpRequest->user();
        abort_unless($admin !== null, 403);

        $validated = $httpRequest->validate([
            'moderator_id' => ['required', 'integer', 'exists:program_moderators,id'],
        ]);

        $moderator = ProgramModerator::findOrFail((int) $validated['moderator_id']);

        $userId = $moderator->user_id;
        $moderator->delete();

        if (! ProgramModerator::where('user_id', $userId)->exists()) {
            User::where('id', $userId)
                ->where('role', UserRole::Moderator)
                ->update(['role' => UserRole::Student]);
        }

        $logAudit->handle($admin, 'moderator.remove', 'User', $userId, []);

        session()->flash('status', 'Moderator removed from program.');

        return redirect()->route('admin.dashboard');
    }

    public function suspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $admin = $httpRequest->user();
        abort_unless($admin !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'days' => ['required', 'integer', 'min:1', 'max:365'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $target = User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->handle($admin, $target, (int) $validated['days'], $validated['reason'] ?? null);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'User suspended.');

        return redirect()->route('admin.dashboard');
    }

    public function unsuspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $admin = $httpRequest->user();
        abort_unless($admin !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $target = User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->unsuspend($admin, $target);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'User unsuspended.');

        return redirect()->route('admin.dashboard');
    }
}
