<?php

namespace Database\Seeders;

use App\Models\Hotspot;
use App\Models\PointOfInterest;
use App\Models\PriorityConfig;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ResponseLog;
use App\Models\User;
use App\Models\Verification;
use App\Services\AnalyticsService;
use App\Services\HotspotImporter;
use App\Services\PriorityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $labels = ['w_verification' => 'Tingkat verifikasi', 'w_landtype' => 'Jenis lahan', 'w_hotspot' => 'Kedekatan hotspot', 'w_poi' => 'Kedekatan fasilitas', 'w_support' => 'Usia dan dukungan', 'w_photo' => 'Bukti foto'];
        foreach (config('apikawal.priority.weights') as $name => $value) {
            PriorityConfig::updateOrCreate(['name' => $name], ['value' => $value, 'label' => $labels[$name]]);
        }
        if (! env('SEED_DEMO', false)) {
            return;
        }

        $demo = [
            ['warga@apikawal.test', 'Warga Demo', 'warga', 'none'],
            ['relawan@apikawal.test', 'Relawan Demo', 'relawan', 'approved'],
            ['petugas@apikawal.test', 'Petugas Demo', 'petugas', 'none'],
            ['admin@apikawal.test', 'Admin Demo', 'admin', 'none'],
        ];
        foreach (range(1, 8) as $index) {
            $demo[] = [sprintf('warga%02d@apikawal.test', $index), 'Warga Simulasi '.$index, 'warga', 'none'];
        }
        $users = [];
        foreach ($demo as [$email, $name, $role, $volunteer]) {
            $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('Demo#2026'), 'is_active' => true]);
            $user->role = $role;
            $user->volunteer_status = $volunteer;
            $user->save();
            $users[] = $user;
        }

        $bounds = config('apikawal.demo_bounds');
        for ($i = 0; $i < 32; $i++) {
            $name = 'POI Simulasi '.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            PointOfInterest::updateOrCreate(['name' => $name], ['kind' => ['permukiman', 'sekolah', 'faskes'][$i % 3], 'lat' => $bounds['south'] + (($i * 17) % 58) / 100, 'lng' => $bounds['west'] + (($i * 23) % 78) / 100, 'is_simulated' => true]);
        }

        $hotspotCsv = database_path('../data/hotspots_hist.csv');
        if (is_file($hotspotCsv) && ! Hotspot::exists()) {
            app(HotspotImporter::class)->import($hotspotCsv);
        } elseif (! Hotspot::where('source', 'SIMULASI')->exists()) {
            for ($i = 0; $i < 300; $i++) {
                $drySeason = [6, 7, 8, 9, 10];
                $month = $drySeason[$i % count($drySeason)];
                Hotspot::create(['lat' => -3.57 + (($i * 37) % 55) / 100, 'lng' => 105.02 + (($i * 53) % 75) / 100, 'confidence' => 45 + ($i % 56), 'detected_at' => now()->subMonths(1 + ($i % 11))->setMonth($month)->subDays($i % 28), 'source' => 'SIMULASI', 'is_simulated' => true]);
            }
            for ($i = 0; $i < 15; $i++) {
                Hotspot::create(['lat' => -3.55 + (($i * 13) % 50) / 100, 'lng' => 105.05 + (($i * 19) % 70) / 100, 'confidence' => 70 + ($i % 31), 'detected_at' => now()->subHours(1 + $i), 'source' => 'SIMULASI', 'is_simulated' => true]);
            }
        }

        $statuses = ['baru', 'baru', 'terverifikasi', 'terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali', 'selesai', 'ditolak', 'padam_sendiri', 'duplikat'];
        $parents = [];
        $reporters = array_slice($users, 4);
        for ($i = 0; $i < 30; $i++) {
            $status = $statuses[$i % count($statuses)];
            $code = 'AK-'.now()->year.'-'.str_pad((string) (900001 + $i), 6, '0', STR_PAD_LEFT);
            $report = Report::updateOrCreate(['code' => $code], [
                'reporter_id' => $reporters[$i % count($reporters)]->id,
                'parent_id' => $status === 'duplikat' ? ($parents[($i - 1) % count($parents)] ?? null) : null,
                'lat' => $bounds['south'] + (($i * 19) % 58) / 100,
                'lng' => $bounds['west'] + (($i * 31) % 78) / 100,
                'land_type' => ['gambut', 'hutan', 'semak', 'kebun', 'lainnya'][$i % 5],
                'signs' => [$i % 2 ? 'asap' : 'api', 'bara'],
                'estimated_area_ha' => ($i % 4) ? null : 0.5 + ($i / 10),
                'description' => 'Laporan kondisi lapangan simulasi nomor '.($i + 1).'.',
                'status' => $status, 'is_simulated' => true, 'needs_moderation' => false,
            ]);
            $parents[] = $report->id;
            if ($i % 5 === 0 && ! $report->photos()->exists()) {
                $photo = $this->placeholderPhoto();
                ReportPhoto::create(['report_id' => $report->id, 'mime' => 'image/jpeg', 'data_base64' => base64_encode($photo), 'size_bytes' => strlen($photo)]);
            }
            if ($i % 4 === 0 && ! $report->verifications()->exists()) {
                foreach (array_slice($users, 0, 3) as $voterIndex => $voter) {
                    if ($voter->id === $report->reporter_id) {
                        continue;
                    }
                    Verification::create(['report_id' => $report->id, 'user_id' => $voter->id, 'vote' => $voterIndex === 2 ? 'tidak_terlihat' : 'konfirmasi', 'weight' => $voterIndex === 1 ? 2 : 1]);
                }
            }
            $this->seedTimeline($report, $status);
        }
        foreach (Report::where('code', 'like', 'AK-'.now()->year.'-900%')->get() as $report) {
            app(PriorityService::class)->recalculate($report);
        }
        app(AnalyticsService::class)->recomputeGrid();
    }

    private function seedTimeline(Report $report, string $target): void
    {
        $paths = [
            'baru' => [], 'terverifikasi' => ['terverifikasi'], 'diteruskan' => ['terverifikasi', 'diteruskan'],
            'dalam_penanganan' => ['terverifikasi', 'diteruskan', 'dalam_penanganan'], 'terkendali' => ['terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali'],
            'selesai' => ['terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali', 'selesai'],
            'ditolak' => ['ditolak'], 'padam_sendiri' => ['padam_sendiri'], 'duplikat' => ['duplikat'],
        ];
        if ($report->logs()->exists()) {
            return;
        }
        $from = null;
        foreach ($paths[$target] as $index => $to) {
            ResponseLog::create(['report_id' => $report->id, 'actor_id' => null, 'from_status' => $from, 'to_status' => $to, 'note' => 'Riwayat simulasi untuk demonstrasi.', 'created_at' => now()->subHours(count($paths[$target]) - $index), 'updated_at' => now()->subHours(count($paths[$target]) - $index)]);
            $from = $to;
        }
    }

    private function placeholderPhoto(): string
    {
        $image = imagecreatetruecolor(640, 400);
        $top = imagecolorallocate($image, 250, 204, 21);
        $bottom = imagecolorallocate($image, 154, 52, 18);
        for ($y = 0; $y < 400; $y++) {
            $ratio = $y / 400;
            $color = imagecolorallocate($image, (int) (250 * (1 - $ratio) + 154 * $ratio), (int) (204 * (1 - $ratio) + 52 * $ratio), (int) (21 * (1 - $ratio) + 18 * $ratio));
            imageline($image, 0, $y, 640, $y, $color);
        }
        imagestring($image, 5, 235, 190, 'FOTO SIMULASI', imagecolorallocate($image, 255, 255, 255));
        ob_start();
        imagejpeg($image, null, 80);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }
}
