<?php

namespace App\Http\Controllers;

use App\Domain\Moderation\Actions\CreateReport;
use App\Domain\Moderation\Actions\LogAudit;
use App\Domain\Moderation\Actions\ResolveReport;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $moderatedProgramIds = ProgramModerator::where('user_id', $user->id)
            ->pluck('program_id');

        $reports = Report::with(['reporter:id,display_name,name', 'reported.room'])
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (Report $report) use ($user, $moderatedProgramIds) {
                if ($user->isAdmin()) {
                    return true;
                }

                if ($moderatedProgramIds->isEmpty()) {
                    return false;
                }

                $entity = $report->reported;

                if ($entity === null) {
                    return false;
                }

                if ($report->reported_type === 'message') {
                    $messageRoom = $entity->room;
                    if ($messageRoom === null) {
                        return false;
                    }

                    return $moderatedProgramIds->contains($messageRoom->program_id);
                }

                if ($report->reported_type === 'resource') {
                    return $moderatedProgramIds->contains($entity->program_id);
                }

                if ($report->reported_type === 'user') {
                    return $moderatedProgramIds->contains($entity->program_id);
                }

                return false;
            })
            ->values();

        $programs = Program::whereIn('id', $moderatedProgramIds)->get(['id', 'code', 'name']);

        return view('moderation.dashboard', [
            'reports' => new \Illuminate\Pagination\LengthAwarePaginator(
                $reports->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 15),
                $reports->count(),
                15,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            ),
            'programs' => $programs,
        ]);
    }

    public function resolve(HttpRequest $httpRequest, Report $report, ResolveReport $resolveReport): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'resolution' => ['required', 'string', 'in:actioned,dismissed'],
            'resolution_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $resolveReport->handle(
                $user,
                $report,
                ReportStatus::from($validated['resolution']),
                $validated['resolution_note'] ?? null
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => 'Could not resolve the report.']);
        }

        session()->flash('status', 'Report resolved.');

        return redirect()->route('moderation.dashboard');
    }

    public function suspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'days' => ['required', 'integer', 'min:1', 'max:90'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $target = \App\Models\User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->handle($user, $target, (int) $validated['days'], $validated['reason'] ?? null);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => 'Could not suspend the user.']);
        }

        session()->flash('status', 'User suspended.');

        return redirect()->route('moderation.dashboard');
    }

    public function unsuspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $target = \App\Models\User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->unsuspend($user, $target);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['error' => 'Could not unsuspend the user.']);
        }

        session()->flash('status', 'User unsuspended.');

        return redirect()->route('moderation.dashboard');
    }
}