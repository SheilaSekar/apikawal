<?php

namespace App\Services;

use App\Domain\StatusMachine;
use App\Enums\Role;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResponseService
{
    public function __construct(private readonly PriorityService $priority) {}

    public function transition(User $actor, Report $report, string $toStatus, ?string $note): Report
    {
        $role = $actor->role->value;
        if (! in_array($actor->role, [Role::Petugas, Role::Admin], true)) {
            throw ValidationException::withMessages(['status' => 'Perubahan status hanya tersedia bagi petugas.']);
        }

        return DB::transaction(function () use ($actor, $report, $toStatus, $note): Report {
            $locked = Report::query()->lockForUpdate()->findOrFail($report->id);
            $from = $locked->status->value;
            try {
                StatusMachine::assertTransition($from, $toStatus, $actor->role->value, $note);
            } catch (\DomainException $exception) {
                throw ValidationException::withMessages(['status' => $exception->getMessage()]);
            }
            $locked->update(['status' => $toStatus]);
            $locked->logs()->create(['actor_id' => $actor->id, 'from_status' => $from, 'to_status' => $toStatus, 'note' => $note]);
            $locked->reporter->notifications()->create(['report_id' => $locked->id, 'message' => "Status laporan {$locked->code} berubah menjadi {$toStatus}."]);
            $this->priority->recalculate($locked->fresh());

            return $locked->fresh();
        });
    }
}
