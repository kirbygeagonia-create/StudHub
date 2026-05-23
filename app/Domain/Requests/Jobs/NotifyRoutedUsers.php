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
            $user->notify(new RequestRoutedNotification($request));
        }
    }
}
