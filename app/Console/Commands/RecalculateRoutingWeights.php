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

            $fulfillmentCounts = DB::table('resource_requests')
                ->join('subjects', 'resource_requests.subject_id', '=', 'subjects.id')
                ->join('offers', 'resource_requests.id', '=', 'offers.request_id')
                ->join('users', 'offers.offerer_user_id', '=', 'users.id')
                ->selectRaw('users.program_id, resource_requests.subject_id, COUNT(*) as fulfillment_count')
                ->where('resource_requests.status', RequestStatus::Fulfilled->value)
                ->whereNotNull('users.program_id')
                ->groupBy('users.program_id', 'resource_requests.subject_id')
                ->get();

            $weightCap = (float) config('studhub.routing_weight_cap', 1.0);
            $weightBase = (float) config('studhub.routing_weight_base', 0.1);
            $weightMultiplier = (float) config('studhub.routing_weight_multiplier', 0.05);

            $updated = 0;
            $total = $fulfillmentCounts->count();
            $this->output->progressStart($total);

            foreach ($fulfillmentCounts as $row) {
                $programId = (int) $row->program_id;
                $subjectId = (int) $row->subject_id;
                $count = (int) $row->fulfillment_count;

                $weight = min($weightCap, $weightBase + ($count * $weightMultiplier));

                $affected = DB::table('program_subjects')
                    ->where('program_id', $programId)
                    ->where('subject_id', $subjectId)
                    ->update(['weight' => $weight]);

                if ($affected > 0) {
                    $updated++;
                }

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
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
