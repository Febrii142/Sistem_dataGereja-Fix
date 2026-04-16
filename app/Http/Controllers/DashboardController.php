<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJemaat = Member::count();
        $jemaatBaru = Member::where('created_at', '>=', now()->subMonth())->count();
        $lakiLaki = Member::where('jenis_kelamin', 'L')->count();
        $perempuan = Member::where('jenis_kelamin', 'P')->count();

        $monthlyGrowth = Member::query()
            ->get(['created_at'])
            ->groupBy(fn ($member) => $member->created_at->format('Y-m'))
            ->map(fn ($items, $month) => ['month' => $month, 'total' => $items->count()])
            ->values();

        $attendanceStats = Attendance::query()
            ->get(['service_date', 'hadir'])
            ->groupBy(fn ($attendance) => $attendance->service_date->format('Y-m'))
            ->map(function ($items, $month) {
                $presentCount = $items->where('hadir', true)->count();
                $rate = $items->count() > 0 ? round(($presentCount / $items->count()) * 100, 2) : 0;

                return ['month' => $month, 'attendance_rate' => $rate];
            })
            ->values();

        $demografi = [
            'Anak (<18)' => 0,
            'Dewasa (18-59)' => 0,
            'Lansia (60+)' => 0,
        ];

        Member::query()->pluck('tanggal_lahir')->each(function ($tanggalLahir) use (&$demografi) {
            $usia = Carbon::parse($tanggalLahir)->age;

            if ($usia < 18) {
                $demografi['Anak (<18)']++;
            } elseif ($usia < 60) {
                $demografi['Dewasa (18-59)']++;
            } else {
                $demografi['Lansia (60+)']++;
            }
        });

        $recentMembers = Member::query()
            ->latest()
            ->limit(5)
            ->get(['nama', 'kontak', 'jenis_kelamin', 'status', 'created_at']);

        return view('dashboard.index', compact(
            'totalJemaat',
            'jemaatBaru',
            'lakiLaki',
            'perempuan',
            'monthlyGrowth',
            'attendanceStats',
            'demografi',
            'recentMembers'
        ));
    }
}
