<?php

namespace App\Domain\Requests\Jobs;

use App\Domain\Requests\Notifications\RequestRoutedNotification;
use App\Models\ResourceRequest;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyRoutedUsers implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 30;

    public int $timeout = 60;

    /**
     * @param  list<int>  $userIds
     */
    public function __construct(
        public int $requestId,
        public array $userIds,
    ) {}

    public function handle(): void
    {
        $request = ResourceRequest::with('subject', 'requester')->find($this->requestId);

        if ($request === null) {
            return;
        }

        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            $prefs = $user->notification_preferences ?? [];

            // Respect only_urgent preference
            $urgency = $request->urgency;
            if (($prefs['only_urgent'] ?? false) && $urgency instanceof \App\Domain\Requests\Enums\RequestUrgency && $urgency->value !== 'urgent') {
                continue;
            }

            // Respect muted_programs preference
            $muted = $prefs['muted_programs'] ?? [];
            if (in_array($user->program_id, $muted, true)) {
                continue;
            }

            $user->notify(new RequestRoutedNotification($request));
        }
    }
}
