<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_reports')->only(['index']);
        $this->middleware('permission:export_reports')->only(['exportPdf']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFilterState($request);
        $membersQuery = $this->buildFilteredMembersQuery($filters);

        $members = (clone $membersQuery)
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $totalResults = (clone $membersQuery)->count();

        $currentMonthCount = (clone $membersQuery)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonth = now()->copy()->subMonth();
        $lastMonthCount = (clone $membersQuery)
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $percentageChange = $lastMonthCount > 0
            ? round((($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : ($currentMonthCount > 0 ? 100.0 : 0.0);

        $distribution = (clone $membersQuery)
            ->selectRaw('status as label, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($item) use ($totalResults) {
                $label = $item->label === 'aktif' ? 'Sektor Aktif' : 'Sektor Tidak Aktif';
                $percentage = $totalResults > 0 ? round(($item->total / $totalResults) * 100, 1) : 0;

                return (object) [
                    'label' => $label,
                    'total' => $item->total,
                    'percentage' => $percentage,
                ];
            });

        $filterOptions = [
            'ageRanges' => [
                '' => 'Semua Rentang',
                'anak' => 'Anak (3-12)',
                'remaja' => 'Remaja (13-18)',
                'dewasa' => 'Dewasa (19-59)',
                'lansia' => 'Lansia (60+)',
            ],
            'genders' => [
                '' => 'Semua Gender',
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
            ],
            'birthdayMonths' => collect(range(1, 12))
                ->mapWithKeys(fn (int $month) => [(string) $month => Carbon::create()->month($month)->translatedFormat('F')])
                ->prepend('Semua Bulan', '')
                ->all(),
        ];

        return view('reports.index', compact(
            'members',
            'filters',
            'filterOptions',
            'totalResults',
            'percentageChange',
            'distribution'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->getFilterState($request);
        $membersQuery = $this->buildFilteredMembersQuery($filters);

        $members = (clone $membersQuery)
            ->orderBy('nama')
            ->get();

        $totalResults = $members->count();

        $distribution = $members
            ->groupBy('status')
            ->map(function ($items, $status) use ($totalResults) {
                $label = $status === 'aktif' ? 'Sektor Aktif' : 'Sektor Tidak Aktif';
                $count = $items->count();

                return (object) [
                    'label' => $label,
                    'total' => $count,
                    'percentage' => $totalResults > 0 ? round(($count / $totalResults) * 100, 1) : 0,
                ];
            })
            ->values();

        $pdf = Pdf::loadView('reports.pdf', [
            'members' => $members,
            'filters' => $filters,
            'totalResults' => $totalResults,
            'distribution' => $distribution,
        ]);

        return $pdf->download('laporan-analisis-demografi.pdf');
    }

    private function getFilterState(Request $request): array
    {
        return [
            'search' => $request->string('search')->toString(),
            'age_range' => $request->string('age_range')->toString(),
            'gender' => $request->string('gender')->toString(),
            'birthday_month' => $request->string('birthday_month')->toString(),
        ];
    }

    private function buildFilteredMembersQuery(array $filters): Builder
    {
        $query = Member::query();

        $query->when($filters['search'] !== '', function (Builder $query) use ($filters) {
            $query->where('nama', 'like', '%'.$filters['search'].'%');
        });

        $query->when($filters['gender'] !== '', function (Builder $query) use ($filters) {
            $query->where('jenis_kelamin', $filters['gender']);
        });

        $query->when($filters['birthday_month'] !== '', function (Builder $query) use ($filters) {
            $query->whereMonth('tanggal_lahir', (int) $filters['birthday_month']);
        });

        $query->when($filters['age_range'] !== '', function (Builder $query) use ($filters) {
            $today = Carbon::today();

            match ($filters['age_range']) {
                'anak' => $query->whereBetween('tanggal_lahir', [
                    $today->copy()->subYears(13)->addDay()->toDateString(),
                    $today->copy()->subYears(3)->toDateString(),
                ]),
                'remaja' => $query->whereBetween('tanggal_lahir', [
                    $today->copy()->subYears(19)->addDay()->toDateString(),
                    $today->copy()->subYears(13)->toDateString(),
                ]),
                'dewasa' => $query->whereBetween('tanggal_lahir', [
                    $today->copy()->subYears(60)->addDay()->toDateString(),
                    $today->copy()->subYears(19)->toDateString(),
                ]),
                'lansia' => $query->whereDate('tanggal_lahir', '<=', $today->copy()->subYears(60)->toDateString()),
                default => null,
            };
        });

        return $query;
    }
}
