<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;

class RecomputeGridCommand extends Command
{
    protected $signature = 'apikawal:recompute-grid';

    protected $description = 'Hitung ulang skor wilayah rawan berdasarkan hotspot dan laporan.';

    public function handle(AnalyticsService $analytics): int
    {
        $count = $analytics->recomputeGrid();
        $this->info("{$count} sel grid dihitung ulang.");

        return self::SUCCESS;
    }
}
