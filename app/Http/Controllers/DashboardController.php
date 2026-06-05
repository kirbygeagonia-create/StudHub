<?php

namespace App\Http\Controllers;

use App\Domain\Reputation\Enums\BadgeTier;
use App\Models\Announcement;
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

        $announcements = Announcement::where('school_id', $user->school_id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'karma' => $karma,
            'badge' => $badge,
            'announcements' => $announcements,
        ]);
    }
}
