<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    private const REPORT_PAGE_SIZE = 10;

    public function __construct()
    {
        $this->middleware('permission:view_reports')->only(['index']);
        $this->middleware('permission:export_reports')->only(['exportPdf']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFilterState($request);
        $activeTab = $filters['tab'] === 'status' ? 'status' : 'demografi';
        $membersQuery = $this->buildFilteredMembersQuery($filters);
        $statusMembersQuery = $this->buildStatusMembersQuery($filters);

        $members = (clone $membersQuery)
            ->orderBy('nama')
            ->paginate(self::REPORT_PAGE_SIZE)
            ->withQueryString();
        $statusMembers = (clone $statusMembersQuery)
            ->orderBy('nama')
            ->paginate(self::REPORT_PAGE_SIZE, ['*'], 'status_page')
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
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($item) use ($totalResults) {
                $formattedLabel = 'Status '.$this->statusLabel($item->status);
                $percentage = $totalResults > 0 ? round(($item->total / $totalResults) * 100, 1) : 0;

                return (object) [
                    'label' => $formattedLabel,
                    'total' => $item->total,
                    'percentage' => $percentage,
                ];
            });

        $statusTotals = Member::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $statusGrandTotal = (int) $statusTotals->sum();
        $statusSummary = collect(Member::STATUSES)->map(function (string $status) use ($statusTotals, $statusGrandTotal) {
            $total = (int) ($statusTotals[$status] ?? 0);

            return (object) [
                'status' => $status,
                'label' => $this->statusLabel($status),
                'total' => $total,
                'percentage' => $statusGrandTotal > 0 ? round(($total / $statusGrandTotal) * 100, 1) : 0,
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
            'statusMembers',
            'filters',
            'activeTab',
            'filterOptions',
            'totalResults',
            'percentageChange',
            'distribution',
            'statusSummary'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->getFilterState($request);
        $activeTab = $filters['tab'] === 'status' ? 'status' : 'demografi';

        if ($activeTab === 'status') {
            $members = $this->buildStatusMembersQuery($filters)
                ->orderBy('nama')
                ->get();

            $statusTotals = Member::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
            $statusGrandTotal = (int) $statusTotals->sum();
            $statusSummary = collect(Member::STATUSES)->map(function (string $status) use ($statusTotals, $statusGrandTotal) {
                $total = (int) ($statusTotals[$status] ?? 0);

                return (object) [
                    'status' => $status,
                    'label' => $this->statusLabel($status),
                    'total' => $total,
                    'percentage' => $statusGrandTotal > 0 ? round(($total / $statusGrandTotal) * 100, 1) : 0,
                ];
            });

            $pdf = Pdf::loadView('reports.pdf', [
                'members' => $members,
                'filters' => $filters,
                'activeTab' => $activeTab,
                'statusSummary' => $statusSummary,
            ]);

            return $pdf->download('laporan-status-jemaat.pdf');
        }

        $filterLabels = $this->mapFilterLabels($filters);
        $membersQuery = $this->buildFilteredMembersQuery($filters);

        $members = (clone $membersQuery)
            ->orderBy('nama')
            ->get();

        $totalResults = $members->count();

        $distribution = $members
            ->groupBy('status')
            ->map(function ($items, $status) use ($totalResults) {
                $formattedLabel = 'Status '.$this->statusLabel($status);
                $count = $items->count();

                return (object) [
                    'label' => $formattedLabel,
                    'total' => $count,
                    'percentage' => $totalResults > 0 ? round(($count / $totalResults) * 100, 1) : 0,
                ];
            })
            ->values();

        $pdf = Pdf::loadView('reports.pdf', [
            'members' => $members,
            'filters' => $filters,
            'activeTab' => $activeTab,
            'filterLabels' => $filterLabels,
            'totalResults' => $totalResults,
            'distribution' => $distribution,
        ]);

        return $pdf->download('laporan-analisis-demografi.pdf');
    }

    private function getFilterState(Request $request): array
    {
        return [
            'tab' => $request->string('tab')->toString(),
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
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

        $query->when($filters['status'] !== '', function (Builder $query) use ($filters) {
            $query->where('status', $filters['status']);
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

    private function buildStatusMembersQuery(array $filters): Builder
    {
        return Member::query()
            ->when($filters['search'] !== '', function (Builder $query) use ($filters) {
                $search = $filters['search'];
                $query->where(function (Builder $query) use ($search) {
                    $query->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('kontak', 'like', '%'.$search.'%')
                        ->orWhere('alamat', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'] !== '', function (Builder $query) use ($filters) {
                $query->where('status', $filters['status']);
            });
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'pindah' => 'Pindah',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function mapFilterLabels(array $filters): array
    {
        $monthLabel = 'Semua';
        if ($filters['birthday_month'] !== '') {
            $monthLabel = Carbon::create()->month((int) $filters['birthday_month'])->translatedFormat('F');
        }

        return [
            'age_range' => [
                '' => 'Semua',
                'anak' => 'Anak (3-12)',
                'remaja' => 'Remaja (13-18)',
                'dewasa' => 'Dewasa (19-59)',
                'lansia' => 'Lansia (60+)',
            ][$filters['age_range']] ?? 'Semua',
            'gender' => [
                '' => 'Semua',
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
            ][$filters['gender']] ?? 'Semua',
            'birthday_month' => $monthLabel,
        ];
    }
}
