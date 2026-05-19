<?php

namespace App\Domain\Moderation\Actions;

use App\Domain\Moderation\Enums\ReportedType;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Domain\Reputation\Actions\AwardKarma;
use App\Domain\Reputation\Enums\KarmaEventReason;
use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ResolveReport
{
    public function handle(User $moderator, Report $report, ReportStatus $resolution, ?string $resolutionNote = null): void
    {
        if (! $report->isOpen()) {
            throw new RuntimeException('This report has already been resolved.');
        }

        DB::transaction(function () use ($moderator, $report, $resolution, $resolutionNote): void {
            $report->update([
                'status' => $resolution,
                'handled_by_user_id' => $moderator->id,
                'resolution_note' => $resolutionNote,
            ]);

            (new LogAudit)->handle(
                $moderator,
                'report.' . $resolution->value,
                'Report',
                $report->id,
                [
                    'reason' => $report->reason,
                    'reported_type' => $report->reported_type,
                    'reported_id' => $report->reported_id,
                ]
            );

            if ($resolution === ReportStatus::Actioned) {
                $reportedUser = $this->getReportedUser($report);
                if ($reportedUser !== null) {
                    (new AwardKarma)->handle(
                        $reportedUser,
                        KarmaEventReason::ReportConfirmed,
                        $report->id,
                        'Report'
                    );
                }

                $reportedType = ReportedType::tryFrom($report->reported_type);

                if ($reportedType === ReportedType::Message) {
                    $report->reported?->delete();
                } elseif ($reportedType === ReportedType::Resource) {
                    $report->reported?->update(['availability' => 'archived']);
                }
            }
        });
    }

    private function getReportedUser(Report $report): ?User
    {
        /** @var ChatMessage|LearningResource|null $reported */
        $reported = $report->reported;
        $reportedType = ReportedType::tryFrom($report->reported_type);

        if ($reportedType === null) {
            return null;
        }

        return match ($reportedType) {
            ReportedType::Message => $reported?->sender,
            ReportedType::Resource => $reported?->owner,
            ReportedType::User => User::find($report->reported_id),
        };
    }
}
