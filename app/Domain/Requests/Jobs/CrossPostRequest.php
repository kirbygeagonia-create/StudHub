<?php

namespace App\Domain\Requests\Jobs;

use App\Models\Program;
use App\Models\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CrossPostRequest implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $requestId,
        public int $programId,
    ) {}

    public function handle(): void
    {
        $request = Request::with('subject', 'requester')->find($this->requestId);
        $program = Program::find($this->programId);

        if ($request === null || $program === null) {
            return;
        }

        $chatRoom = $program->chatRooms()
            ->where('kind', 'program')
            ->first();

        if ($chatRoom === null) {
            return;
        }

        $subjectName = $request->subject?->name ?? 'Unknown subject';
        $requesterName = $request->requester?->preferredDisplayName() ?? 'Someone';

        $chatRoom->messages()->create([
            'sender_id' => $request->requester_user_id,
            'body' => sprintf(
                "📌 Routed request: %s needs a **%s** for *%s*. [Open request →](%s)",
                $requesterName,
                str_replace('_', ' ', $request->type_wanted),
                $subjectName,
                route('requests.show', $request),
            ),
            'is_system' => true,
        ]);
    }
}