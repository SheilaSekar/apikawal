<?php

namespace App\Services;

use App\Domain\DuplicateMatcher;
use App\Enums\Role;
use App\Enums\VolunteerStatus;
use App\Models\Report;
use App\Models\User;
use App\Models\Verification;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function __construct(private readonly ImageProcessor $images, private readonly PriorityService $priority, private readonly VerificationService $verification) {}

    public function checkDuplicate(User $user, float $lat, float $lng): array
    {
        $box = Geo::boundingBox($lat, $lng, (float) config('apikawal.duplicate.radius_km'));
        $cutoff = now()->subHours((int) config('apikawal.duplicate.window_hours'));
        $candidates = Report::query()->whereNull('parent_id')->whereNotIn('status', config('apikawal.duplicate.closed_statuses'))
            ->where('created_at', '>=', $cutoff)->whereBetween('lat', [$box['min_lat'], $box['max_lat']])
            ->whereBetween('lng', [$box['min_lng'], $box['max_lng']])->get(['id', 'code', 'reporter_id', 'lat', 'lng', 'status', 'created_at']);
        $own = Report::query()->where('reporter_id', $user->id)->where('created_at', '>=', $cutoff)
            ->whereBetween('lat', [$box['min_lat'], $box['max_lat']])->whereBetween('lng', [$box['min_lng'], $box['max_lng']])
            ->get(['id', 'code', 'reporter_id', 'lat', 'lng', 'status', 'created_at'])->map(function (Report $report): array {
                $candidate = $this->candidate($report);
                $candidate['status'] = 'baru';

                return $candidate;
            });
        $match = DuplicateMatcher::find($lat, $lng, (int) $user->id, $candidates->concat($own)->map(fn (Report $report) => $report instanceof Report ? $this->candidate($report) : $report));

        return ['kind' => $match['kind'], 'code' => $match['report']['code'] ?? null];
    }

    public function create(User $user, array $data): Report
    {
        $box = Geo::boundingBox((float) $data['lat'], (float) $data['lng'], (float) config('apikawal.duplicate.radius_km'));
        $cutoff = now()->subHours((int) config('apikawal.duplicate.window_hours'));
        $roots = Report::query()->whereNull('parent_id')->whereNotIn('status', config('apikawal.duplicate.closed_statuses'))
            ->where('created_at', '>=', $cutoff)->whereBetween('lat', [$box['min_lat'], $box['max_lat']])
            ->whereBetween('lng', [$box['min_lng'], $box['max_lng']])->get(['id', 'reporter_id', 'lat', 'lng', 'status', 'created_at']);

        // Tolak laporan berulang dari pelapor yang sama, termasuk laporan yang sebelumnya menjadi duplikat.
        $ownReports = Report::query()->where('reporter_id', $user->id)->where('created_at', '>=', $cutoff)
            ->whereBetween('lat', [$box['min_lat'], $box['max_lat']])->whereBetween('lng', [$box['min_lng'], $box['max_lng']])
            ->get(['id', 'reporter_id', 'lat', 'lng', 'status', 'created_at']);
        $ownMatch = DuplicateMatcher::find((float) $data['lat'], (float) $data['lng'], (int) $user->id, $ownReports->map(function (Report $r): array {
            $candidate = $this->candidate($r);
            $candidate['status'] = 'baru';

            return $candidate;
        }));
        if ($ownMatch['kind'] === 'same_reporter') {
            throw ValidationException::withMessages(['lat' => 'Anda sudah melaporkan kejadian di sekitar lokasi ini dalam enam jam terakhir.']);
        }

        $match = DuplicateMatcher::find((float) $data['lat'], (float) $data['lng'], (int) $user->id, $roots->map(fn (Report $r) => $this->candidate($r)));
        if ($match['kind'] === 'same_reporter') {
            throw ValidationException::withMessages(['lat' => 'Anda sudah melaporkan kejadian di sekitar lokasi ini dalam enam jam terakhir.']);
        }
        $parent = $match['kind'] === 'duplicate' ? Report::findOrFail($match['report']['id']) : null;
        if ($parent && Verification::where('report_id', $parent->id)->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages(['lat' => 'Anda sudah memberi dukungan pada laporan serupa ini.']);
        }
        $processedPhotos = collect($data['photos'] ?? [])->map(fn ($file) => $this->images->process($file));

        return DB::transaction(function () use ($user, $data, $parent, $processedPhotos): Report {
            $report = Report::create([
                'reporter_id' => $user->id, 'parent_id' => $parent?->id, 'lat' => (float) $data['lat'], 'lng' => (float) $data['lng'],
                'land_type' => $data['land_type'], 'signs' => $data['signs'], 'estimated_area_ha' => $data['estimated_area_ha'] ?? null,
                'description' => $data['description'] ?? null, 'status' => $parent ? 'duplikat' : 'baru',
            ]);
            $report->update(['code' => sprintf('AK-%d-%06d', now()->year, $report->id)]);
            foreach ($processedPhotos as $photo) {
                $report->photos()->create($photo);
            }
            $this->priority->recalculate($report);

            if ($parent) {
                $weight = match ($user->role) {
                    Role::Petugas, Role::Admin => 3,
                    Role::Relawan => $user->volunteer_status === VolunteerStatus::Approved ? 2 : 1,
                    default => 1,
                };
                $parent->verifications()->create(['user_id' => $user->id, 'vote' => 'konfirmasi', 'weight' => $weight, 'source_report_id' => $report->id]);
                $this->verification->applyOutcome($parent);
                $this->priority->recalculate($parent->fresh());
            }

            return $report->load('photos');
        });
    }

    private function candidate(Report $report): array
    {
        return ['id' => $report->id, 'code' => $report->code, 'reporter_id' => $report->reporter_id, 'lat' => $report->lat, 'lng' => $report->lng,
            'status' => $report->status->value, 'created_at' => $report->created_at];
    }
}
