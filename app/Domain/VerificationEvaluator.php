<?php

namespace App\Domain;

final class VerificationEvaluator
{
    /** Mengembalikan status baru bila bobot suara memenuhi ambang yang transparan. */
    public static function evaluate(iterable $votes): array
    {
        $confirm = 0;
        $deny = 0;
        $out = 0;
        $trustedConfirm = false;
        foreach ($votes as $vote) {
            $weight = (int) $vote['weight'];
            if ($vote['vote'] === 'konfirmasi') {
                $confirm += $weight;
                $trustedConfirm = $trustedConfirm || in_array($vote['role'] ?? '', ['relawan', 'petugas', 'admin'], true);
            } elseif ($vote['vote'] === 'tidak_terlihat') {
                $deny += $weight;
            } elseif ($vote['vote'] === 'sudah_padam') {
                $out += $weight;
            }
        }

        $status = null;
        if ($trustedConfirm || ($confirm >= (int) config('apikawal.verification.confirm_threshold', 2) && $confirm > $deny)) {
            $status = 'terverifikasi';
        } elseif ($deny >= 3 && $deny > $confirm) {
            $status = 'ditolak';
        } elseif ($out >= 2 && $out >= $confirm) {
            $status = 'padam_sendiri';
        }

        return ['status' => $status, 'confirm_w' => $confirm, 'deny_w' => $deny, 'out_w' => $out];
    }
}
