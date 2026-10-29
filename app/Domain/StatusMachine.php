<?php

namespace App\Domain;

final class StatusMachine
{
    private const TRANSITIONS = [
        'baru' => ['terverifikasi', 'ditolak', 'padam_sendiri', 'duplikat'],
        'terverifikasi' => ['diteruskan', 'padam_sendiri'],
        'diteruskan' => ['dalam_penanganan'],
        'dalam_penanganan' => ['terkendali'],
        'terkendali' => ['selesai'],
    ];

    public static function canTransition(string $from, string $to, string $role, ?string $note = null): bool
    {
        if ($from === $to) {
            return false;
        }
        if ($role === 'admin') {
            return filled($note);
        }
        if ($role === 'sistem') {
            return in_array($to, ['terverifikasi', 'ditolak', 'padam_sendiri', 'duplikat'], true) && $from === 'baru'
                || ($from === 'terverifikasi' && $to === 'padam_sendiri');
        }
        if ($role === 'petugas' && $from === 'terverifikasi' && $to === 'padam_sendiri') {
            return true;
        }
        if ($from === 'terverifikasi' && $to === 'diteruskan' && in_array($role, ['petugas', 'admin'], true)) {
            return filled($note);
        }

        return in_array($to, self::TRANSITIONS[$from] ?? [], true) && in_array($role, ['petugas', 'admin'], true);
    }

    public static function assertTransition(string $from, string $to, string $role, ?string $note = null): void
    {
        if (! self::canTransition($from, $to, $role, $note)) {
            throw new \DomainException('Perubahan status tidak diizinkan atau catatan override admin belum diisi.');
        }
    }
}
