<?php

namespace App\Policies;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function view(?User $user, Report $report): bool
    {
        return ! $report->needs_moderation || ($user && ($user->id === $report->reporter_id || in_array($user->role, [Role::Petugas, Role::Admin], true)));
    }

    public function create(User $user): bool
    {
        return (bool) $user->newQuery()->whereKey($user->getKey())->value('is_active');
    }

    public function verify(User $user, Report $report): bool
    {
        return $user->id !== $report->reporter_id && in_array($report->status, [ReportStatus::Baru, ReportStatus::Terverifikasi], true);
    }
}
