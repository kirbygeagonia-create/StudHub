<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Identity\Rules\AllowedSchoolEmailDomain;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:190',
                'unique:' . User::class,
                new AllowedSchoolEmailDomain,
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $school = School::where('code', 'SEAIT')->first();

        $user = User::create([
            'school_id' => $school?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Student->value,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('onboarding.show', absolute: false));
    }
}
