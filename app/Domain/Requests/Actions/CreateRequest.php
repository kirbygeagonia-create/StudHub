<?php

namespace App\Domain\Requests\Actions;

use App\Domain\Requests\Enums\RequestStatus;
use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\Request;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateRequest
{
    /**
     * @param  array{
     *     subject_id: int,
     *     type_wanted: string,
     *     urgency?: string,
     *     needed_by?: string|null,
     *     description?: string|null,
     * }  $data
     */
    public function handle(User $requester, array $data): Request
    {
        if (! $requester->school_id) {
            throw new RuntimeException('Requester must belong to a school.');
        }

        /** @var Subject|null $subject */
        $subject = Subject::where('id', $data['subject_id'])
            ->where('school_id', $requester->school_id)
            ->first();

        if ($subject === null) {
            throw new RuntimeException('Subject does not exist for this school.');
        }

        $openCount = Request::where('requester_user_id', $requester->id)
            ->whereIn('status', RequestStatus::openValues())
            ->count();

        if ($openCount >= 5) {
            throw new RuntimeException('You already have 5 open requests. Please resolve or withdraw some first.');
        }

        $recentRequest = Request::where('requester_user_id', $requester->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($recentRequest !== null) {
            throw new RuntimeException('You can only post one request every 10 minutes. Please wait before posting again.');
        }

        return DB::transaction(function () use ($requester, $subject, $data): Request {
            $request = Request::create([
                'requester_user_id' => $requester->id,
                'subject_id' => $subject->id,
                'type_wanted' => $data['type_wanted'],
                'urgency' => $data['urgency'] ?? RequestUrgency::Normal->value,
                'needed_by' => $data['needed_by'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => RequestStatus::Open->value,
            ]);

            return $request;
        });
    }
}