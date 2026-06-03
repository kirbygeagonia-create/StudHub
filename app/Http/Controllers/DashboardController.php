<?php

namespace App\Http\Controllers;

use App\Domain\Reputation\Enums\BadgeTier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $karma = $user->karma ?? 0;
        $badge = BadgeTier::fromKarmaOrNull($karma);

        return view('dashboard', [
            'user' => $user,
            'karma' => $karma,
            'badge' => $badge,
        ]);
    }
}
