<?php

namespace App\Services;

use App\Domain\VerificationEvaluator;
use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\VolunteerStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerificationService
{
    public function __construct(private readonly PriorityService $priority) {}

    public function verify(User $actor, Report $report, string $vote, ?float $distanceKm = null): Report
    {
        return DB::transaction(function () use ($actor, $report, $vote, $distanceKm): Report {
            $report = Report::query()->lockForUpdate()->findOrFail($report->id);
            if (! $actor->can('verify', $report)) {
                throw ValidationException::withMessages(['vote' => 'Anda tidak dapat memverifikasi laporan ini.']);
            }
            if ($report->verifications()->where('user_id', $actor->id)->exists()) {
                throw ValidationException::withMessages(['vote' => 'Anda sudah memberi suara pada laporan ini.']);
            }
            $role = $actor->role;
            $weight = match ($role) {
                Role::Petugas, Role::Admin => 3,
                Role::Relawan => $actor->volunteer_status === VolunteerStatus::Approved ? 2 : 1,
                default => 1,
            };
            $report->verifications()->create(['user_id' => $actor->id, 'vote' => $vote, 'weight' => $weight, 'distance_km' => $distanceKm]);
            $this->applyOutcome($report);
            $this->priority->recalculate($report->fresh());

            return $report->fresh();
        });
    }

    /** Dipakai juga setelah laporan duplikat otomatis menjadi suara konfirmasi. */
    public function applyOutcome(Report $report): array
    {
        $votes = $report->verifications()->with('user')->get()->map(function ($vote): array {
            $role = $vote->user->role->value;
            if ($role === 'relawan' && $vote->user->volunteer_status !== VolunteerStatus::Approved) {
                $role = 'warga';
            }

            return ['vote' => $vote->vote->value, 'weight' => $vote->weight, 'role' => $role];
        });
        $result = VerificationEvaluator::evaluate($votes, (int) config('apikawal.verification.confirm_threshold'));
        if ($report->status !== ReportStatus::Baru || $result['status'] === null) {
            return $result;
        }

        $from = $report->status;
        $report->update(['status' => $result['status']]);
        $report->logs()->create(['from_status' => $from->value, 'to_status' => $result['status'], 'note' => 'Status diperbarui berdasarkan hasil verifikasi.']);
        $report->reporter()->increment('reputation', $result['status'] === 'terverifikasi' ? 1 : -1);
        $report->reporter->notifications()->create(['report_id' => $report->id, 'message' => "Status laporan {$report->code} berubah menjadi {$result['status']}."]);

        if ($result['status'] === 'terverifikasi') {
            $officers = User::query()->where('role', Role::Petugas->value)->where('is_active', true)->get();
            foreach ($officers as $officer) {
                $officer->notifications()->create(['report_id' => $report->id, 'message' => "Laporan {$report->code} telah terverifikasi."]);
            }
        }

        return $result;
    }
}
