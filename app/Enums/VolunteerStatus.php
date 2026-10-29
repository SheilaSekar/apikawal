<?php

namespace App\Enums;

enum VolunteerStatus: string
{
    case None = 'none';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Belum mengajukan', self::Pending => 'Menunggu persetujuan', self::Approved => 'Disetujui', self::Rejected => 'Ditolak',
        };
    }
}
