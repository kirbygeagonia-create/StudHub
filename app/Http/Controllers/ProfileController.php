<?php

namespace App\Http\Controllers;

use App\Domain\Identity\ValueObjects\NotificationPreferences;
use App\Domain\Reputation\Enums\BadgeTier;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show top sharers leaderboard per program.
     */
    public function leaderboard(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $programId = $request->integer('program_id', $user->program_id);

        $programs = Program::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $validProgram = $programs->firstWhere('id', $programId);
        if ($validProgram === null) {
            $programId = $user->program_id;
        }

        $topSharers = User::where('program_id', $programId)
            ->whereNotNull('onboarded_at')
            ->orderByDesc('karma')
            ->limit(20)
            ->get(['id', 'display_name', 'name', 'karma', 'year_level']);

        return view('profile.leaderboard', [
            'topSharers' => $topSharers,
            'programs' => $programs,
            'selectedProgramId' => $programId,
        ]);
    }

    /**
     * Display a public user profile (other than self, or self).
     */
    public function publicProfile(Request $request, User $user): View
    {
        $viewer = $request->user();
        abort_unless($viewer !== null, 403);

        if ($user->school_id !== $viewer->school_id) {
            abort(404);
        }

        $user->loadMissing(['school', 'college', 'program']);

        $karma = (int) ($user->karma ?? 0);
        $badge = BadgeTier::fromKarmaOrNull($karma);

        $resourceCount = LearningResource::where('owner_user_id', $user->id)->count();

        $isSelf = $viewer->id === $user->id;

        return view('profile.public', [
            'user' => $user,
            'karma' => $karma,
            'badge' => $badge,
            'resourceCount' => $resourceCount,
            'isSelf' => $isSelf,
        ]);
    }

    /**
     * Display the user's profile (read-only).
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user?->loadMissing(['school', 'college', 'program']);

        $karma = (int) ($user?->karma ?? 0);
        $badge = BadgeTier::fromKarmaOrNull($karma);

        $resourceCount = $user !== null
            ? LearningResource::where('owner_user_id', $user->id)->count()
            : 0;

        return view('profile.show', [
            'user' => $user,
            'karma' => $karma,
            'badge' => $badge,
            'resourceCount' => $resourceCount,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Show notification preferences form.
     */
    public function notificationPreferences(Request $request): View
    {
        $user = $request->user();
        $prefs = $user?->notification_preferences;

        return view('profile.notification-preferences', [
            'user' => $user,
            'prefs' => $prefs,
        ]);
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $validated = $request->validate([
            'only_urgent' => ['nullable', 'boolean'],
            'muted_programs' => ['nullable', 'array'],
            'muted_programs.*' => ['integer', 'exists:programs,id'],
            'digest_enabled' => ['nullable', 'boolean'],
        ]);

        $prefs = new NotificationPreferences(
            onlyUrgent: (bool) ($validated['only_urgent'] ?? false),
            mutedPrograms: $validated['muted_programs'] ?? [],
            digestEnabled: (bool) ($validated['digest_enabled'] ?? true),
        );

        $user->fill(['notification_preferences' => $prefs->toArray()])->save();

        session()->flash('status', 'Notification preferences updated.');

        return Redirect::route('profile.notification-preferences');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
