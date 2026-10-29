<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Baru = 'baru';
    case Terverifikasi = 'terverifikasi';
    case Diteruskan = 'diteruskan';
    case DalamPenanganan = 'dalam_penanganan';
    case Terkendali = 'terkendali';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Duplikat = 'duplikat';
    case PadamSendiri = 'padam_sendiri';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru', self::Terverifikasi => 'Terverifikasi', self::Diteruskan => 'Diteruskan',
            self::DalamPenanganan => 'Dalam penanganan', self::Terkendali => 'Terkendali', self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak', self::Duplikat => 'Duplikat', self::PadamSendiri => 'Padam sendiri',
        };
    }
}
