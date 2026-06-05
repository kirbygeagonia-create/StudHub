<?php

namespace App\Domain\Search\Jobs;

use App\Domain\Identity\ValueObjects\NotificationPreferences;
use App\Mail\DailyDigest;
use App\Models\RequestRoute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendDailyDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $backoff = 60;

    public int $timeout = 120;

    public function handle(): void
    {
        $programsWithRoutes = RequestRoute::query()
            ->whereDate('created_at', now()->toDateString())
            ->select('program_id')
            ->distinct()
            ->pluck('program_id');

        if ($programsWithRoutes->isEmpty()) {
            return;
        }

        $users = User::query()
            ->whereIn('program_id', $programsWithRoutes)
            ->whereNotNull('onboarded_at')
            ->whereNull('suspended_until')
            ->get();

        $chatActivityByProgram = DB::table('chat_messages')
            ->select('users.program_id', DB::raw('COUNT(*) as count'))
            ->join('users', 'users.id', '=', 'chat_messages.sender_id')
            ->whereDate('chat_messages.created_at', now()->toDateString())
            ->groupBy('users.program_id')
            ->pluck('count', 'program_id');

        $requestCounts = RequestRoute::query()
            ->whereDate('created_at', now()->toDateString())
            ->selectRaw('program_id, COUNT(*) as count')
            ->groupBy('program_id')
            ->pluck('count', 'program_id');

        foreach ($users as $user) {
            $prefs = NotificationPreferences::fromArray($user->notification_preferences ?? []);
            if (! $prefs->digestEnabled) {
                continue;
            }

            $programId = $user->program_id;
            $requestCount = (int) ($requestCounts[$programId] ?? 0);
            $chatActivity = (int) ($chatActivityByProgram[$programId] ?? 0);

            if ($requestCount === 0 && $chatActivity === 0) {
                continue;
            }

            Mail::to($user)->queue(new DailyDigest($user, [
                'request_count' => $requestCount,
                'chat_activity' => $chatActivity,
                'active_programs' => $programsWithRoutes->count(),
            ]));
        }
    }
}
