<?php

namespace App\Domain\Moderation\Actions;

use App\Domain\Moderation\Enums\ReportStatus;
use App\Domain\Reputation\Actions\AwardKarma;
use App\Domain\Reputation\Enums\KarmaEventReason;
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

                if ($report->reported_type === 'message') {
                    $report->reported?->delete();
                } elseif ($report->reported_type === 'resource') {
                    $report->reported?->update(['availability' => 'archived']);
                }
            }
        });
    }

    private function getReportedUser(Report $report): ?User
    {
        return match ($report->reported_type) {
            'message' => $report->reported?->sender,
            'resource' => $report->reported?->owner,
            'user' => User::find($report->reported_id),
            default => null,
        };
    }
}