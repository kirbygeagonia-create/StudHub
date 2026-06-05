<?php

namespace App\Console\Commands;

use App\Domain\Requests\Enums\RequestStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateRoutingWeights extends Command
{
    protected $signature = 'studhub:recalc-routing-weights';

    protected $description = 'Recalculate program_subjects.weight from real fulfillment data';

    public function handle(): int
    {
        try {
            $this->info('Recalculating routing weights from fulfillment data...');

            $fulfillmentCounts = DB::table('requests')
                ->join('subjects', 'requests.subject_id', '=', 'subjects.id')
                ->join('offers', 'requests.id', '=', 'offers.request_id')
                ->join('users', 'offers.offerer_user_id', '=', 'users.id')
                ->selectRaw('users.program_id, requests.subject_id, COUNT(*) as fulfillment_count')
                ->where('requests.status', RequestStatus::Fulfilled->value)
                ->whereNotNull('users.program_id')
                ->groupBy('users.program_id', 'requests.subject_id')
                ->get();

            $weightCap = (float) config('studhub.routing_weight_cap', 1.0);
            $weightBase = (float) config('studhub.routing_weight_base', 0.1);
            $weightMultiplier = (float) config('studhub.routing_weight_multiplier', 0.05);

            $upsertData = [];
            foreach ($fulfillmentCounts as $row) {
                $programId = (int) $row->program_id;
                $subjectId = (int) $row->subject_id;
                $count = (int) $row->fulfillment_count;

                $weight = min($weightCap, $weightBase + ($count * $weightMultiplier));

                $upsertData[] = [
                    'program_id' => $programId,
                    'subject_id' => $subjectId,
                    'weight' => $weight,
                ];
            }

            $chunks = array_chunk($upsertData, 100);
            $updated = 0;
            foreach ($chunks as $chunk) {
                DB::table('program_subjects')->upsert(
                    $chunk,
                    ['program_id', 'subject_id'],
                    ['weight']
                );
                $updated += count($chunk);
            }

            $this->info("Updated weights for {$updated} program-subject pairs.");
            Log::info('Routing weights recalculated', ['pairs_updated' => $updated]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Recalculation failed: ' . $e->getMessage());
            Log::error('Routing weight recalculation failed', ['exception' => $e->getMessage()]);

            return self::FAILURE;
        }
    }
}
