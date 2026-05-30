<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Actions\LogAudit;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\College;
use App\Models\Feedback;
use App\Models\Program;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeanController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $collegeId = $user->college_id;

        $programs = Program::where('college_id', $collegeId)->get(['id', 'code', 'name']);
        $programIds = $programs->pluck('id');

        $totalStudents = User::whereIn('program_id', $programIds)
            ->whereNotNull('onboarded_at')
            ->count();
        $totalModerators = User::where('role', UserRole::Moderator->value)
            ->whereIn('program_id', $programIds)
            ->count();
        $totalProgramHeads = User::where('role', UserRole::ProgramHead->value)
            ->where('college_id', $collegeId)
            ->count();
        $unreadFeedback = Feedback::where('recipient_role', 'dean')
            ->where('recipient_college_id', $collegeId)
            ->whereNull('read_at')
            ->count();
        $openReports = Report::where('status', ReportStatus::Open->value)
            ->where('school_id', $user->school_id)
            ->count();

        $college = College::find($collegeId);

        return view('dean.dashboard', [
            'college' => $college,
            'programs' => $programs,
            'totalStudents' => $totalStudents,
            'totalModerators' => $totalModerators,
            'totalProgramHeads' => $totalProgramHeads,
            'unreadFeedback' => $unreadFeedback,
            'openReports' => $openReports,
        ]);
    }

    public function feedback(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        // Mark all unread feedback for this college as read
        Feedback::where('recipient_role', 'dean')
            ->where('recipient_college_id', $user->college_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $feedbacks = Feedback::with('user:id,display_name,name,email,program_id')
            ->where('recipient_role', 'dean')
            ->where('recipient_college_id', $user->college_id)
            ->latest()
            ->paginate(25);

        return view('dean.feedback', ['feedbacks' => $feedbacks]);
    }

    public function resolveFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        if ($feedback->recipient_college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This feedback is not for your college.']);
        }

        $feedback->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_note' => $httpRequest->input('resolution_note'),
        ]);

        session()->flash('status', 'Feedback resolved.');

        return redirect()->route('dean.feedback');
    }

    public function escalateFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        if ($feedback->recipient_college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This feedback is not for your college.']);
        }

        DB::transaction(function () use ($feedback): void {
            $feedback->update(['status' => 'escalated']);

            Feedback::create([
                'user_id' => $feedback->user_id,
                'type' => $feedback->type,
                'body' => $feedback->body,
                'recipient_role' => 'sao',
                'recipient_college_id' => null,
                'recipient_program_id' => null,
                'escalated_from_id' => $feedback->id,
                'status' => 'open',
            ]);
        });

        session()->flash('status', 'Feedback escalated to SAO.');

        return redirect()->route('dean.feedback');
    }

    public function programs(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $programs = Program::withCount([
            'users as student_count' => function ($q): void {
                $q->whereNotNull('onboarded_at');
            },
        ])->where('college_id', $user->college_id)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $programHeads = User::where('role', UserRole::ProgramHead->value)
            ->where('college_id', $user->college_id)
            ->get(['id', 'name', 'display_name', 'program_id']);

        return view('dean.programs', [
            'programs' => $programs,
            'programHeads' => $programHeads,
        ]);
    }

    public function assignProgramHead(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $targetUser = User::findOrFail((int) $validated['user_id']);
        $program = Program::findOrFail((int) $validated['program_id']);

        // Dean can only assign program heads within their own college
        if ($program->college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This program is not under your college.']);
        }

        if ($targetUser->college_id !== $user->college_id) {
            return redirect()->back()->withErrors(['error' => 'This user is not in your college.']);
        }

        DB::transaction(function () use ($targetUser, $program): void {
            $targetUser->update([
                'role' => UserRole::ProgramHead,
                'college_id' => $program->college_id,
                'program_id' => null,
                'year_level' => null,
            ]);
        });

        $logAudit->handle($user, 'program_head.assign', 'User', $targetUser->id, [
            'program_id' => $program->id,
        ]);

        session()->flash('status', 'Program Head assigned.');

        return redirect()->route('dean.programs');
    }
}
