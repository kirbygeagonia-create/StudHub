<?php

namespace App\Http\Controllers;

use App\Domain\Moderation\Actions\ResolveReport;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportedType;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\Report;
use App\Models\User;
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

        if ($user->isAdmin()) {
            $reports = Report::with(['reporter:id,display_name,name', 'reported'])
                ->where('status', 'open')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $reports = Report::with(['reporter:id,display_name,name', 'reported'])
                ->where('status', 'open')
                ->where(function ($query) use ($moderatedProgramIds): void {
                    $query->where(function ($q) use ($moderatedProgramIds): void {
                        // Messages: chat_rooms.program_id IN (...)
                        $q->where('reported_type', ReportedType::Message->value)
                            ->whereExists(function ($sq) use ($moderatedProgramIds): void {
                                $sq->selectRaw('1')
                                    ->from('chat_messages')
                                    ->join('chat_rooms', 'chat_rooms.id', '=', 'chat_messages.chat_room_id')
                                    ->whereColumn('chat_messages.id', 'reports.reported_id')
                                    ->whereIn('chat_rooms.program_id', $moderatedProgramIds);
                            });
                    })->orWhere(function ($q) use ($moderatedProgramIds): void {
                        // Resources: resources.program_id IN (...)
                        $q->where('reported_type', ReportedType::Resource->value)
                            ->whereExists(function ($sq) use ($moderatedProgramIds): void {
                                $sq->selectRaw('1')
                                    ->from('resources')
                                    ->whereColumn('resources.id', 'reports.reported_id')
                                    ->whereIn('resources.program_id', $moderatedProgramIds);
                            });
                    })->orWhere(function ($q) use ($moderatedProgramIds): void {
                        // Users: users.program_id IN (...)
                        $q->where('reported_type', ReportedType::User->value)
                            ->whereExists(function ($sq) use ($moderatedProgramIds): void {
                                $sq->selectRaw('1')
                                    ->from('users')
                                    ->whereColumn('users.id', 'reports.reported_id')
                                    ->whereIn('users.program_id', $moderatedProgramIds);
                            });
                    });
                })
                ->orderByDesc('created_at')
                ->get();
        }

        $programs = Program::whereIn('id', $moderatedProgramIds)->get(['id', 'code', 'name']);

        return view('moderation.dashboard', [
            'reports' => new LengthAwarePaginator(
                $reports->forPage(Paginator::resolveCurrentPage(), 15),
                $reports->count(),
                15,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
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
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
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

        $target = User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->handle($user, $target, (int) $validated['days'], $validated['reason'] ?? null);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
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

        $target = User::findOrFail((int) $validated['user_id']);

        try {
            $suspendUser->unsuspend($user, $target);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'User unsuspended.');

        return redirect()->route('moderation.dashboard');
    }
}
