<?php

namespace App\Domain\Moderation\Actions;

use App\Domain\Moderation\Enums\ReportedType;
use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Report;
use App\Models\User;
use RuntimeException;

class CreateReport
{
    public function handle(User $reporter, ReportedType $reportedType, int $reportedId, string $reason, ?string $notes = null): Report
    {
        $resource = $this->resolveReported($reportedType, $reportedId);

        if ($resource === null) {
            throw new RuntimeException('The reported entity does not exist.');
        }

        $existing = Report::where('reporter_user_id', $reporter->id)
            ->where('reported_type', $reportedType->value)
            ->where('reported_id', $reportedId)
            ->where('status', 'open')
            ->exists();

        if ($existing) {
            throw new RuntimeException('You have already reported this item.');
        }

        return Report::create([
            'reporter_user_id' => $reporter->id,
            'reported_type' => $reportedType->value,
            'reported_id' => $reportedId,
            'reason' => $reason,
            'notes' => $notes,
            'status' => 'open',
        ]);
    }

    private function resolveReported(ReportedType $type, int $id): ?object
    {
        return match ($type) {
            ReportedType::Message => ChatMessage::find($id),
            ReportedType::Resource => LearningResource::find($id),
            ReportedType::User => User::find($id),
        };
    }
}
