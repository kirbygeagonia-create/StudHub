<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Actions\LogAudit;
use App\Models\College;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaoController extends Controller
{
    public function dashboard(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $totalUsers = User::where('school_id', $user->school_id)->count();
        $totalStudents = User::where('role', UserRole::Student->value)
            ->where('school_id', $user->school_id)->count();
        $totalModerators = User::where('role', UserRole::Moderator->value)
            ->where('school_id', $user->school_id)->count();
        $totalProgramHeads = User::where('role', UserRole::ProgramHead->value)
            ->where('school_id', $user->school_id)->count();
        $totalDeans = User::where('role', UserRole::Dean->value)
            ->where('school_id', $user->school_id)->count();
        $colleges = College::withCount([
            'programs as program_count',
            'users as active_user_count' => function ($q): void {
                $q->whereNotNull('onboarded_at')->whereNull('suspended_until');
            },
        ])->where('school_id', $user->school_id)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $chartData = collect(range(6, 0))->map(function (int $daysAgo) use ($user): array {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'count' => User::where('school_id', $user->school_id)
                    ->whereDate('last_seen_at', $date->toDateString())
                    ->count(),
            ];
        })->values()->all();

        return view('sao.dashboard', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalModerators' => $totalModerators,
            'totalProgramHeads' => $totalProgramHeads,
            'totalDeans' => $totalDeans,
            'colleges' => $colleges,
            'chartData' => $chartData,
        ]);
    }

    public function feedback(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        // Mark all unread SAO + SuperAdmin feedback as read
        Feedback::whereIn('recipient_role', ['sao', 'super_admin'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $feedbacks = Feedback::with('user:id,display_name,name,email,program_id')
            ->whereIn('recipient_role', ['sao', 'super_admin'])
            ->latest()
            ->paginate(25);

        return view('sao.feedback', [
            'feedbacks' => $feedbacks,
            'unreadFeedback' => 0,
        ]);
    }

    public function resolveFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $feedback->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_note' => $httpRequest->input('resolution_note'),
        ]);

        session()->flash('status', 'Feedback resolved.');

        return redirect()->route('sao.feedback');
    }

    public function users(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $query = User::with(['college:id,code,name', 'program:id,code,name'])
            ->where('school_id', $user->school_id);

        // Search/filter
        if ($search = $httpRequest->input('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        if ($role = $httpRequest->input('role')) {
            $query->where('role', $role);
        }

        if ($collegeId = $httpRequest->input('college_id')) {
            $query->where('college_id', (int) $collegeId);
        }

        $users = $query->orderBy('name')->paginate(25);
        $colleges = College::where('school_id', $user->school_id)->get(['id', 'code', 'name']);

        return view('sao.users', [
            'users' => $users,
            'colleges' => $colleges,
        ]);
    }

    public function assignRole(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', 'in:' . implode(',', [
                UserRole::Dean->value,
                UserRole::ProgramHead->value,
                UserRole::Moderator->value,
                UserRole::Student->value,
            ])],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'college_id' => ['nullable', 'integer', 'exists:colleges,id'],
        ]);

        $target = User::findOrFail((int) $validated['user_id']);

        // Prevent duplicate Dean per college
        if ($validated['role'] === UserRole::Dean->value) {
            $alreadyHasDean = User::where('role', UserRole::Dean->value)
                ->where('college_id', $validated['college_id'])
                ->where('id', '!=', $target->id)
                ->exists();

            if ($alreadyHasDean) {
                return redirect()->back()->withErrors([
                    'error' => 'This college already has a Dean assigned. Remove the existing Dean first.',
                ]);
            }
        }

        // Prevent duplicate Program Head per college
        if ($validated['role'] === UserRole::ProgramHead->value) {
            $alreadyHasProgramHead = User::where('role', UserRole::ProgramHead->value)
                ->where('college_id', $validated['college_id'])
                ->where('id', '!=', $target->id)
                ->exists();

            if ($alreadyHasProgramHead) {
                return redirect()->back()->withErrors([
                    'error' => 'This college already has a Program Head assigned. Remove the existing Program Head first.',
                ]);
            }
        }

        $updateData = ['role' => UserRole::from($validated['role'])];

        if (isset($validated['program_id'])) {
            $updateData['program_id'] = (int) $validated['program_id'];
        }
        if (isset($validated['college_id'])) {
            $updateData['college_id'] = (int) $validated['college_id'];
        }

        DB::transaction(function () use ($target, $updateData): void {
            $target->forceFill($updateData)->save();
        });

        $logAudit->handle($user, 'user.role.assign', 'User', $target->id, [
            'new_role' => $validated['role'],
        ]);

        session()->flash('status', 'User role updated.');

        return redirect()->route('sao.users');
    }

    public function announcements(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        return view('sao.announcements');
    }
}
