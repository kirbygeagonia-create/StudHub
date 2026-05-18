<?php

namespace App\Domain\Lends\Actions;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Requests\Enums\RequestStatus;
use App\Models\LearningResource;
use App\Models\Lend;
use App\Models\Offer;
use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordLend
{
    public function handle(Request $request, Offer $offer, User $requester, ?string $returnBy = null): Lend
    {
        if ($request->requester_user_id !== $requester->id) {
            throw new RuntimeException('Only the requester can record a lend.');
        }

        if ($offer->request_id !== $request->id) {
            throw new RuntimeException('This offer does not belong to this request.');
        }

        if ($request->status !== RequestStatus::Matched) {
            throw new RuntimeException('The request must be in matched status to record a lend.');
        }

        if ($request->fulfilled_offer_id !== $offer->id) {
            throw new RuntimeException('Only the accepted offer can be recorded as a lend.');
        }

        if ($offer->resource_id === null) {
            throw new RuntimeException('This offer does not include a resource to lend.');
        }

        return DB::transaction(function () use ($request, $offer, $returnBy) {
            $request = Request::lockForUpdate()->findOrFail($request->id);

            if ($request->status !== RequestStatus::Matched) {
                throw new RuntimeException('The request must be in matched status to record a lend.');
            }

            $resource = LearningResource::lockForUpdate()->findOrFail($offer->resource_id);

            if ($resource->owner_user_id !== $offer->offerer_user_id) {
                throw new RuntimeException('The offered resource does not belong to the offerer.');
            }

            $existingLend = Lend::where('offer_id', $offer->id)
                ->whereNull('returned_at')
                ->lockForUpdate()
                ->first();

            if ($existingLend !== null) {
                throw new RuntimeException('A lend has already been recorded for this offer.');
            }

            $lend = Lend::create([
                'resource_id' => $resource->id,
                'offer_id' => $offer->id,
                'request_id' => $request->id,
                'from_user_id' => $offer->offerer_user_id,
                'to_user_id' => $request->requester_user_id,
                'lent_at' => now(),
                'return_by' => $returnBy,
            ]);

            $resource->update([
                'availability' => ResourceAvailability::LentOut,
            ]);
            $resource->increment('lend_count');

            $request->update([
                'status' => RequestStatus::Fulfilled,
            ]);

            return $lend;
        });
    }
}
