<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJemaat = Member::count();
        $aktif = Member::where('status', 'aktif')->count();
        $tidakAktif = $totalJemaat - $aktif;

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

        return view('dashboard.index', compact(
            'totalJemaat',
            'aktif',
            'tidakAktif',
            'monthlyGrowth',
            'attendanceStats'
        ));
    }
}
