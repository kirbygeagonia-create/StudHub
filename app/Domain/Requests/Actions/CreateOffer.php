<?php

namespace App\Domain\Requests\Actions;

use App\Domain\Requests\Enums\OfferStatus;
use App\Domain\Requests\Enums\RequestStatus;
use App\Models\LearningResource;
use App\Models\Offer;
use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateOffer
{
    /**
     * @param  array{
     *     resource_id?: int|null,
     *     message?: string|null,
     * }  $data
     */
    public function handle(User $offerer, Request $request, array $data): Offer
    {
        if (! $offerer->school_id) {
            throw new RuntimeException('User must belong to a school.');
        }

        if ($offerer->id === $request->requester_user_id) {
            throw new RuntimeException('You cannot offer on your own request.');
        }

        if (! $request->isOpen()) {
            throw new RuntimeException('This request is no longer accepting offers.');
        }

        $existing = Offer::where('request_id', $request->id)
            ->where('offerer_user_id', $offerer->id)
            ->exists();

        if ($existing) {
            throw new RuntimeException('You have already made an offer on this request.');
        }

        if (! empty($data['resource_id'])) {
            /** @var LearningResource|null $resource */
            $resource = LearningResource::where('id', $data['resource_id'])
                ->where('owner_user_id', $offerer->id)
                ->where('school_id', $offerer->school_id)
                ->first();

            if ($resource === null) {
                throw new RuntimeException('Resource not found or does not belong to you.');
            }
        }

        return DB::transaction(function () use ($offerer, $request, $data): Offer {
            return Offer::create([
                'request_id' => $request->id,
                'offerer_user_id' => $offerer->id,
                'resource_id' => $data['resource_id'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => OfferStatus::Pending->value,
            ]);
        });
    }
}