<?php

namespace App\Domain\Moderation\Actions;

use App\Domain\Moderation\Enums\ReportedType;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateReport
{
    public function handle(User $reporter, ReportedType $reportedType, int $reportedId, string $reason, ?string $notes = null): Report
    {
        if ($reportedType === ReportedType::User && $reportedId === $reporter->id) {
            throw new RuntimeException('You cannot report yourself.');
        }

        $resource = $this->resolveReported($reporter, $reportedType, $reportedId);

        if ($resource === null) {
            throw new RuntimeException('The reported entity does not exist.');
        }

        if ($reportedType === ReportedType::Message && $resource instanceof ChatMessage && $resource->sender_id === $reporter->id) {
            throw new RuntimeException('You cannot report your own message.');
        }

        if ($reportedType === ReportedType::Resource && $resource instanceof LearningResource && $resource->owner_user_id === $reporter->id) {
            throw new RuntimeException('You cannot report your own resource.');
        }

        return DB::transaction(function () use ($reporter, $reportedType, $reportedId, $reason, $notes): Report {
            $existing = Report::where('reporter_user_id', $reporter->id)
                ->where('reported_type', $reportedType->value)
                ->where('reported_id', $reportedId)
                ->where('status', ReportStatus::Open->value)
                ->exists();

            if ($existing) {
                throw new RuntimeException('You have already reported this item.');
            }

            return Report::create([
                'reporter_user_id' => $reporter->id,
                'school_id' => $reporter->school_id,
                'reported_type' => $reportedType->value,
                'reported_id' => $reportedId,
                'reason' => $reason,
                'notes' => $notes,
                'status' => ReportStatus::Open->value,
            ]);
        });
    }

    private function resolveReported(User $reporter, ReportedType $type, int $id): ?object
    {
        return match ($type) {
            ReportedType::Message => ChatMessage::whereKey($id)
                ->whereHas('room', fn ($q) => $q->where('school_id', $reporter->school_id))
                ->first(),
            ReportedType::Resource => LearningResource::whereKey($id)
                ->where('school_id', $reporter->school_id)
                ->first(),
            ReportedType::User => User::whereKey($id)
                ->where('school_id', $reporter->school_id)
                ->first(),
        };
    }
}
