<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $demografi = Member::query()
            ->selectRaw('jenis_kelamin, COUNT(*) as total')
            ->groupBy('jenis_kelamin')
            ->get();

        $pertumbuhan = Member::query()
            ->get(['created_at'])
            ->groupBy(fn ($member) => $member->created_at->format('Y-m'))
            ->map(fn ($items, $bulan) => (object) ['bulan' => $bulan, 'total' => $items->count()])
            ->values();

        $kehadiran = Attendance::query()
            ->with('member')
            ->latest('service_date')
            ->take(30)
            ->get();

        return view('reports.index', compact('demografi', 'pertumbuhan', 'kehadiran'));
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('reports.pdf', [
            'demografi' => Member::query()->selectRaw('jenis_kelamin, COUNT(*) as total')->groupBy('jenis_kelamin')->get(),
            'pertumbuhan' => Member::query()
                ->get(['created_at'])
                ->groupBy(fn ($member) => $member->created_at->format('Y-m'))
                ->map(fn ($items, $bulan) => (object) ['bulan' => $bulan, 'total' => $items->count()])
                ->values(),
            'kehadiran' => Attendance::query()->with('member')->latest('service_date')->take(50)->get(),
        ]);

        return $pdf->download('laporan-analytics.pdf');
    }
}
