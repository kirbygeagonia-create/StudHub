<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        $schoolCode = config('studhub.school_code', 'SEAIT');
        $school = School::where('code', $schoolCode)->firstOrFail();

        $programs = Program::query()
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->with('college')
            ->orderBy('code')
            ->get();

        return view('onboarding.show', [
            'programs' => $programs,
            'yearMin' => (int) config('studhub.year_level_min', 1),
            'yearMax' => (int) config('studhub.year_level_max', 5),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $yearMin = (int) config('studhub.year_level_min', 1);
        $yearMax = (int) config('studhub.year_level_max', 5);

        // SAO and SuperAdmin: only need display_name
        if ($user->isSao() || $user->isSuperAdmin()) {
            $validated = Validator::make($request->all(), [
                'display_name' => ['required', 'string', 'min:2', 'max:120'],
            ])->validate();

            $user->forceFill([
                'display_name' => $validated['display_name'],
                'onboarded_at' => now(),
            ])->save();

            return redirect()->route('dashboard')->with('status', 'onboarding-complete');
        }

        // Dean and Program Head: need display_name + college (via program selection for college_id)
        if ($user->isDean() || $user->isProgramHead()) {
            $validated = Validator::make($request->all(), [
                'program_id' => ['required', 'integer', 'exists:programs,id'],
                'display_name' => ['required', 'string', 'min:2', 'max:120'],
            ])->validate();

            $program = Program::with('college')->findOrFail($validated['program_id']);

            $user->forceFill([
                'school_id' => $user->school_id ?? $program->school_id,
                'college_id' => $program->college_id,
                'display_name' => $validated['display_name'],
                'onboarded_at' => now(),
            ])->save();

            return redirect()->route('dashboard')->with('status', 'onboarding-complete');
        }

        // Students and Moderators: need full profile
        $validated = Validator::make($request->all(), [
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'year_level' => ['required', 'integer', 'between:' . $yearMin . ',' . $yearMax],
            'display_name' => ['required', 'string', 'min:2', 'max:120'],
        ])->validate();

        $program = Program::with('college')->findOrFail($validated['program_id']);

        $user->forceFill([
            'school_id' => $user->school_id ?? $program->school_id,
            'college_id' => $program->college_id,
            'program_id' => $program->id,
            'year_level' => $validated['year_level'],
            'display_name' => $validated['display_name'],
            'onboarded_at' => now(),
        ])->save();

        return redirect()->route('dashboard')->with('status', 'onboarding-complete');
    }
}
