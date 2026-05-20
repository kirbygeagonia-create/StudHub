<?php

namespace App\Http\Controllers;

use App\Domain\Moderation\Actions\ResolveReport;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $moderatedProgramIds = ProgramModerator::where('user_id', $user->id)
            ->pluck('program_id');

        $query = Report::query()
            ->with(['reporter:id,display_name,name', 'reported'])
            ->where('status', ReportStatus::Open->value);

        if (! $user->isAdmin()) {
            if ($moderatedProgramIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $programIds = $moderatedProgramIds->all();

                $query->where(function ($q) use ($programIds): void {
                    $q->whereHasMorph(
                        'reported',
                        [ChatMessage::class],
                        function ($qq) use ($programIds): void {
                            $qq->whereHas(
                                'room',
                                fn ($r) => $r->whereIn('program_id', $programIds)
                            );
                        }
                    )->orWhereHasMorph(
                        'reported',
                        [LearningResource::class, User::class],
                        function ($qq) use ($programIds): void {
                            $qq->whereIn('program_id', $programIds);
                        }
                    );
                });
            }
        }

        $reports = $query->orderByDesc('created_at')->paginate(15);
        $programs = Program::whereIn('id', $moderatedProgramIds)->get(['id', 'code', 'name']);

        return view('moderation.dashboard', [
            'reports' => $reports,
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
