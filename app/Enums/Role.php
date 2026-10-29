<?php

namespace App\Enums;

enum Role: string
{
    case Warga = 'warga';
    case Relawan = 'relawan';
    case Petugas = 'petugas';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Warga => 'Warga', self::Relawan => 'Relawan', self::Petugas => 'Petugas', self::Admin => 'Admin',
        };
    }
}
