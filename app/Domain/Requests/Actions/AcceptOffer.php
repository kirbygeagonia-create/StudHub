<?php

namespace App\Domain\Requests\Actions;

use App\Domain\Reputation\Actions\AwardKarma;
use App\Domain\Reputation\Enums\KarmaEventReason;
use App\Domain\Requests\Enums\OfferStatus;
use App\Domain\Requests\Enums\RequestStatus;
use App\Models\Offer;
use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AcceptOffer
{
    public function handle(User $requester, Request $request, Offer $offer): void
    {
        if ($request->requester_user_id !== $requester->id) {
            throw new RuntimeException('Only the requester can accept an offer.');
        }

        if ($offer->request_id !== $request->id) {
            throw new RuntimeException('This offer does not belong to this request.');
        }

        if (! $request->isOpen()) {
            throw new RuntimeException('This request is no longer accepting offers.');
        }

        if ($offer->status !== OfferStatus::Pending) {
            throw new RuntimeException('This offer has already been acted upon.');
        }

        DB::transaction(function () use ($request, $offer): void {
            $offer->update(['status' => OfferStatus::Accepted]);

            Offer::where('request_id', $request->id)
                ->where('id', '!=', $offer->id)
                ->where('status', OfferStatus::Pending)
                ->update(['status' => OfferStatus::Rejected]);

            $request->update([
                'status' => RequestStatus::Matched,
                'fulfilled_offer_id' => $offer->id,
            ]);

            $offerer = User::find($offer->offerer_user_id);
            if ($offerer !== null) {
                (new AwardKarma)->handle($offerer, KarmaEventReason::RequestFulfilled, $offer->id, 'Offer');
            }
        });
    }
}