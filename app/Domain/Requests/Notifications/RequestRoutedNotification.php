<?php

namespace App\Domain\Requests\Notifications;

use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\ResourceRequest;
use App\Models\User;
use Illuminate\Notifications\Notification;

class RequestRoutedNotification extends Notification
{
    public function __construct(public ResourceRequest $request) {}

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
            'urgency' => $this->request->urgency instanceof RequestUrgency
                ? $this->request->urgency->value
                : 'normal',
            'message' => sprintf(
                '%s needs help with "%s"',
                $this->request->requester?->preferredDisplayName() ?? 'Someone',
                $this->request->subject?->name ?? 'Unknown subject',
            ),
        ];
    }
}
