<?php

namespace App\Services;

use App\Models\Hotspot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HotspotImporter
{
    public function import(string $path): int
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw ValidationException::withMessages(['file' => 'Berkas CSV tidak dapat dibaca.']);
        }
        $header = fgetcsv($handle);
        $expected = ['detected_at', 'lat', 'lng', 'confidence', 'source'];
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }
        if (! $header || array_slice($header, 0, 5) !== $expected) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'Kolom wajib: detected_at,lat,lng,confidence,source.']);
        }
        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            if (count($line) < 5 || ! is_numeric($line[1]) || ! is_numeric($line[2])) {
                continue;
            }
            $lat = (float) $line[1];
            $lng = (float) $line[2];
            $confidence = is_numeric($line[3]) ? (int) $line[3] : null;
            try {
                $detectedAt = Carbon::parse($line[0])->toDateTimeString();
            } catch (\Throwable) {
                continue;
            }
            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($confidence !== null && ($confidence < 0 || $confidence > 100)) || trim($line[4]) === '') {
                continue;
            }
            $rows[] = ['detected_at' => $detectedAt, 'lat' => $lat, 'lng' => $lng, 'confidence' => $confidence, 'source' => mb_substr($line[4], 0, 255), 'is_simulated' => false];
        }
        fclose($handle);
        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 200) as $chunk) {
                Hotspot::insert($chunk);
            }
        });

        return count($rows);
    }
}
