<?php

namespace App\Domain\Requests\Jobs;

use App\Domain\Identity\ValueObjects\NotificationPreferences;
use App\Domain\Requests\Enums\RequestUrgency;
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
            $rawPrefs = $user->notification_preferences;
            /** @var array<string, mixed> $prefsArray */
            $prefsArray = is_array($rawPrefs) ? $rawPrefs : [];
            $prefs = NotificationPreferences::fromArray($prefsArray);

            // Respect only_urgent preference
            $urgency = $request->urgency;
            if ($prefs->onlyUrgent && $urgency instanceof RequestUrgency && $urgency->value !== 'urgent') {
                continue;
            }

            // Respect muted_programs preference
            if ($user->program_id !== null && $prefs->isProgramMuted($user->program_id)) {
                continue;
            }

            $user->notify(new RequestRoutedNotification($request));
        }
    }
}
