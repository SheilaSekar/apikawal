<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VolunteerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminActionRequest;
use App\Http\Requests\Admin\UpdatePriorityWeightsRequest;
use App\Models\AbuseFlag;
use App\Models\GridCell;
use App\Models\PriorityConfig;
use App\Models\Report;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\AnalyticsService;
use App\Services\HotspotImporter;
use App\Services\ResponseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage', User::class);

        return view('pages.admin', [
            'users' => User::latest()->paginate(15, ['*'], 'users'),
            'volunteerApplications' => UserNotification::with('user')->where('message', 'like', 'Pengajuan relawan:%')->whereNull('read_at')->latest()->limit(20)->get(),
            'flags' => AbuseFlag::with(['report', 'reporter'])->where('resolved', false)->latest()->limit(20)->get(),
            'moderation' => Report::with('reporter')->where('needs_moderation', true)->latest()->limit(20)->get(),
            'rejected' => Report::with('reporter')->where('status', 'ditolak')->latest()->limit(20)->get(),
            'weights' => PriorityConfig::all()->keyBy('name'),
            'gridCount' => GridCell::count(),
        ]);
    }

    public function updateUser(AdminActionRequest $request, User $user): RedirectResponse
    {
        $this->authorize('manage', User::class);
        $user->role = $request->validated('role');
        $user->is_active = (bool) $request->validated('is_active');
        if ($request->filled('volunteer_status')) {
            $user->volunteer_status = VolunteerStatus::from($request->validated('volunteer_status'));
            if ($user->volunteer_status === VolunteerStatus::Approved) {
                $user->role = 'relawan';
            } elseif ($user->volunteer_status === VolunteerStatus::Rejected && $user->role->value === 'relawan') {
                $user->role = 'warga';
            }
        }
        if ($user->role->value === 'relawan' && $user->volunteer_status === VolunteerStatus::Pending) {
            $user->volunteer_status = VolunteerStatus::Approved;
        }
        if (in_array($user->volunteer_status, [VolunteerStatus::Approved, VolunteerStatus::Rejected], true)) {
            UserNotification::where('message', 'like', '%'.$user->email.'%')->where('message', 'like', 'Pengajuan relawan:%')->update(['read_at' => now()]);
        }
        $user->save();

        return back()->with('status', 'Akun diperbarui.');
    }

    public function moderate(AdminActionRequest $request, Report $report, ResponseService $responses): RedirectResponse
    {
        $this->authorize('manage', User::class);
        $approved = (bool) $request->validated('approved');
        DB::transaction(function () use ($approved, $request, $report, $responses) {
            if (! $approved) {
                $responses->transition($request->user(), $report, 'ditolak', $request->validated('reason'));
            }
            $report->update(['needs_moderation' => false]);
        });

        return back()->with('status', $approved ? 'Laporan disetujui.' : 'Laporan ditolak.');
    }

    public function resolveFlag(AbuseFlag $flag): RedirectResponse
    {
        $this->authorize('manage', User::class);
        $flag->update(['resolved' => true]);

        return back()->with('status', 'Flag ditandai selesai.');
    }

    public function updateWeights(UpdatePriorityWeightsRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $labels = ['w_verification' => 'Tingkat verifikasi', 'w_landtype' => 'Jenis lahan', 'w_hotspot' => 'Kedekatan hotspot', 'w_poi' => 'Kedekatan fasilitas', 'w_support' => 'Usia dan dukungan', 'w_photo' => 'Bukti foto'];
            foreach ($request->validated('weights') as $key => $value) {
                PriorityConfig::updateOrCreate(['name' => $key], ['value' => $value, 'label' => $labels[$key]]);
            }
        });
        Cache::forget('apikawal.priority.weights');

        return back()->with('status', 'Bobot prioritas diperbarui.');
    }

    public function resetWeights(): RedirectResponse
    {
        $this->authorize('manage', User::class);
        foreach (config('apikawal.priority.weights') as $name => $value) {
            PriorityConfig::updateOrCreate(['name' => $name], ['value' => $value, 'label' => $name]);
        }
        Cache::forget('apikawal.priority.weights');

        return back()->with('status', 'Bobot dikembalikan ke nilai awal.');
    }

    public function importHotspots(AdminActionRequest $request, HotspotImporter $importer): RedirectResponse
    {
        $count = $importer->import($request->file('file')->getRealPath());

        return back()->with('status', "$count hotspot berhasil diimpor.");
    }

    public function recompute(AnalyticsService $analytics): RedirectResponse
    {
        $this->authorize('manage', User::class);
        $count = $analytics->recomputeGrid();

        return back()->with('status', "$count sel wilayah dihitung ulang.");
    }

    public function export(): StreamedResponse
    {
        $this->authorize('manage', User::class);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'wb');
            fputcsv($out, ['kode', 'status', 'prioritas', 'jenis_lahan', 'latitude', 'longitude', 'dibuat']);
            Report::with('reporter')->orderBy('id')->chunk(500, function ($reports) use ($out) {
                foreach ($reports as $report) {
                    fputcsv($out, [$report->code, $report->status->value, $report->priority_score, $report->land_type->value, $report->lat, $report->lng, $report->created_at->toIso8601String()]);
                }
            });
            fclose($out);
        }, 'apikawal-reports.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
