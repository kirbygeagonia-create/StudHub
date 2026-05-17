<?php

namespace App\Domain\Requests\Jobs;

use App\Models\Request;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Notification;

class NotifyRoutedUsers implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<int>  $userIds
     */
    public function __construct(
        public int $requestId,
        public array $userIds,
    ) {}

    public function handle(): void
    {
        $request = Request::with('subject', 'requester')->find($this->requestId);

        if ($request === null) {
            return;
        }

        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            $user->notify(new class($request) extends Notification
            {
                public function __construct(public Request $request) {}

                /**
                 * @return array<int, string>
                 */
                public function via(User $notifiable): array
                {
                    return ['database'];
                }

                /**
                 * @return array<string, mixed>
                 */
                public function toArray(User $notifiable): array
                {
                    return [
                        'type' => 'request_routed',
                        'request_id' => $this->request->id,
                        'subject_name' => $this->request->subject?->name ?? 'Unknown subject',
                        'requester_name' => $this->request->requester?->preferredDisplayName() ?? 'Someone',
                        'urgency' => $this->request->urgency?->value ?? 'normal',
                        'message' => sprintf(
                            '%s needs help with "%s"',
                            $this->request->requester?->preferredDisplayName() ?? 'Someone',
                            $this->request->subject?->name ?? 'Unknown subject',
                        ),
                    ];
                }
            });
        }
    }
}