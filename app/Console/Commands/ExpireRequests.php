<?php

namespace App\Console\Commands;

use App\Domain\Requests\Enums\RequestStatus;
use App\Models\ResourceRequest;
use Illuminate\Console\Command;

class ExpireRequests extends Command
{
    protected $signature = 'studhub:expire-requests';

    protected $description = 'Mark open requests past their needed_by date as expired';

    public function handle(): int
    {
        $count = ResourceRequest::query()
            ->where('status', RequestStatus::Open->value)
            ->whereNotNull('needed_by')
            ->where('needed_by', '<', now())
            ->update(['status' => RequestStatus::Expired->value]);

        $this->info("Expired {$count} stale request(s).");

        return self::SUCCESS;
    }
}
