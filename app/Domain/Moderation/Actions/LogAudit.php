<?php

namespace App\Domain\Moderation\Actions;

use App\Models\AuditLog;
use App\Models\User;

class LogAudit
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function handle(?User $actor, string $action, string $targetType, int $targetId, ?array $metadata = null): void
    {
        AuditLog::create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
