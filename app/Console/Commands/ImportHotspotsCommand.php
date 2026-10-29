<?php

namespace App\Console\Commands;

use App\Services\HotspotImporter;
use Illuminate\Console\Command;

class ImportHotspotsCommand extends Command
{
    protected $signature = 'apikawal:import-hotspots {path}';

    protected $description = 'Impor hotspot dari CSV detected_at,lat,lng,confidence,source';

    public function handle(HotspotImporter $importer): int
    {
        $path = $this->argument('path');
        if (! is_file($path) || ! is_readable($path)) {
            $this->error('Berkas CSV tidak ditemukan atau tidak dapat dibaca.');

            return self::FAILURE;
        }
        $count = $importer->import($path);
        $this->info("{$count} hotspot berhasil diimpor.");

        return self::SUCCESS;
    }
}
