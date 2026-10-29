<?php

namespace App\Enums;

enum VoteType: string
{
    case Konfirmasi = 'konfirmasi';
    case TidakTerlihat = 'tidak_terlihat';
    case SudahPadam = 'sudah_padam';

    public function label(): string
    {
        return match ($this) {
            self::Konfirmasi => 'Terlihat', self::TidakTerlihat => 'Tidak terlihat', self::SudahPadam => 'Sudah padam',
        };
    }
}
