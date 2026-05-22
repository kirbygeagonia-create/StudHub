<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateRoutingWeights extends Command
{
    protected $signature = 'studhub:recalc-routing-weights';

    protected $description = 'Recalculate program_subjects.weight from real fulfillment data';

    public function handle(): void
    {
        $this->info('Recalculating routing weights from fulfillment data...');

        $fulfillmentCounts = DB::table('resource_requests')
            ->join('subjects', 'resource_requests.subject_id', '=', 'subjects.id')
            ->join('offers', 'resource_requests.id', '=', 'offers.request_id')
            ->join('users', 'offers.offerer_user_id', '=', 'users.id')
            ->selectRaw('users.program_id, resource_requests.subject_id, COUNT(*) as fulfillment_count')
            ->where('resource_requests.status', 'fulfilled')
            ->whereNotNull('users.program_id')
            ->groupBy('users.program_id', 'resource_requests.subject_id')
            ->get();

        $updated = 0;

        foreach ($fulfillmentCounts as $row) {
            $programId = (int) $row->program_id;
            $subjectId = (int) $row->subject_id;
            $count = (int) $row->fulfillment_count;

            $weight = min(1.0, 0.1 + ($count * 0.05));

            $affected = DB::table('program_subjects')
                ->where('program_id', $programId)
                ->where('subject_id', $subjectId)
                ->update(['weight' => $weight]);

            if ($affected > 0) {
                $updated++;
            }
        }

        $this->info("Updated weights for {$updated} program-subject pairs.");
        Log::info('Routing weights recalculated', ['pairs_updated' => $updated]);
    }
}
