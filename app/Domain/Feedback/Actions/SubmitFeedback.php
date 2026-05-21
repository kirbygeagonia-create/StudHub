<?php

namespace App\Domain\Feedback\Actions;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubmitFeedback
{
    /**
     * @param  array{b?: string, type?: string}  $data
     */
    public function handle(User $user, array $data): Feedback
    {
        $body = trim($data['b'] ?? '');

        if ($body === '' || mb_strlen($body) < 5) {
            throw new RuntimeException('Feedback must be at least 5 characters.');
        }

        if (mb_strlen($body) > 2000) {
            throw new RuntimeException('Feedback is limited to 2000 characters.');
        }

        $type = in_array($data['type'] ?? '', ['bug', 'feature', 'praise', 'other'], true)
            ? $data['type']
            : 'feedback';

        return DB::transaction(function () use ($user, $body, $type): Feedback {
            return Feedback::create([
                'user_id' => $user->id,
                'type' => $type,
                'body' => $body,
            ]);
        });
    }
}
