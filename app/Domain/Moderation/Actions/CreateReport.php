<?php

namespace App\Domain\Moderation\Actions;

use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Report;
use App\Models\User;
use RuntimeException;

class CreateReport
{
    public function handle(User $reporter, string $reportedType, int $reportedId, string $reason, ?string $notes = null): Report
    {
        $resource = $this->resolveReported($reportedType, $reportedId);

        if ($resource === null) {
            throw new RuntimeException('The reported entity does not exist.');
        }

        $existing = Report::where('reporter_user_id', $reporter->id)
            ->where('reported_type', $reportedType)
            ->where('reported_id', $reportedId)
            ->where('status', 'open')
            ->exists();

        if ($existing) {
            throw new RuntimeException('You have already reported this item.');
        }

        return Report::create([
            'reporter_user_id' => $reporter->id,
            'reported_type' => $reportedType,
            'reported_id' => $reportedId,
            'reason' => $reason,
            'notes' => $notes,
            'status' => 'open',
        ]);
    }

    private function resolveReported(string $type, int $id): mixed
    {
        return match ($type) {
            'message' => ChatMessage::find($id),
            'resource' => LearningResource::find($id),
            'user' => User::find($id),
            default => null,
        };
    }
}
