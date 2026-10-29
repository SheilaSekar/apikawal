<?php

namespace App\Services;

use App\Models\AbuseFlag;
use App\Models\Report;
use App\Models\User;

class AbuseReportService
{
    public function flag(User $user, Report $report, string $reason): AbuseFlag
    {
        return $report->flags()->create(['flagged_by' => $user->id, 'reason' => $reason]);
    }
}
