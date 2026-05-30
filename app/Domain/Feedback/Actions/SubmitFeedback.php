<?php

namespace App\Domain\Feedback\Actions;

use App\Domain\Feedback\Enums\FeedbackType;
use App\Domain\Identity\Enums\UserRole;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubmitFeedback
{
    /**
     * @param  array{body?: string, type?: string}  $data
     */
    public function handle(User $user, array $data): Feedback
    {
        $body = trim($data['body'] ?? '');

        if ($body === '' || mb_strlen($body) < 5) {
            throw new RuntimeException('Feedback must be at least 5 characters.');
        }

        if (mb_strlen($body) > 2000) {
            throw new RuntimeException('Feedback is limited to 2000 characters.');
        }

        $type = FeedbackType::tryFrom($data['type'] ?? '') ?? FeedbackType::General;

        [$recipientRole, $collegeId, $programId] = $this->resolveRecipient($user);

        return DB::transaction(function () use ($user, $body, $type, $recipientRole, $collegeId, $programId): Feedback {
            return Feedback::create([
                'user_id' => $user->id,
                'type' => $type->value,
                'body' => $body,
                'recipient_role' => $recipientRole,
                'recipient_college_id' => $collegeId,
                'recipient_program_id' => $programId,
                'status' => 'open',
            ]);
        });
    }

    /**
     * @return array{0: string, 1: int|null, 2: int|null}
     */
    private function resolveRecipient(User $user): array
    {
        return match ($user->role) {
            // Student/Moderator → Program Head of their college
            UserRole::Student,
            UserRole::Moderator => ['program_head', $user->college_id, null],

            // Program Head → Dean of their college
            UserRole::ProgramHead => ['dean', $user->college_id, null],

            // Dean → SAO
            UserRole::Dean => ['sao', null, null],

            // SAO / SuperAdmin → SuperAdmin (system-level issues only)
            default => ['super_admin', null, null],
        };
    }
}
