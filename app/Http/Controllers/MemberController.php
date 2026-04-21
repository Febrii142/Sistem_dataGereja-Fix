<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Member;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_members')->only(['index', 'show']);
        $this->middleware('permission:create_members')->only(['create', 'store']);
        $this->middleware('permission:edit_members')->only(['edit', 'update']);
        $this->middleware('permission:delete_members')->only(['destroy']);
        $this->middleware('permission:export_members')->only(['exportExcel', 'exportPdf']);
        $this->middleware('permission:import_members')->only(['import']);
    }

    public function index(Request $request)
    {
        $wilayahField = collect(['wilayah', 'kelompok'])
            ->first(fn (string $column) => Schema::hasColumn('members', $column));

        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'gender' => $request->string('gender')->toString(),
            'age_category' => $request->string('age_category')->toString(),
            'year' => $request->string('year')->toString(),
            'wilayah' => $request->string('wilayah')->toString(),
            'wilayah_field' => $wilayahField,
        ];

        $members = Member::query()
            ->filterCategories($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $wilayahOptions = $wilayahField
            ? Member::query()
                ->whereNotNull($wilayahField)
                ->where($wilayahField, '!=', '')
                ->distinct()
                ->orderBy($wilayahField)
                ->pluck($wilayahField)
                ->all()
            : [];

        $driver = Member::query()->getModel()->getConnection()->getDriverName();
        $yearExpression = match ($driver) {
            'sqlite' => "CAST(strftime('%Y', created_at) AS INTEGER)",
            'pgsql' => 'EXTRACT(YEAR FROM created_at)',
            default => 'YEAR(created_at)',
        };

        $yearOptions = Member::query()
            ->whereNotNull('created_at')
            ->selectRaw("{$yearExpression} as year")
            ->groupBy(DB::raw($yearExpression))
            ->orderByRaw("{$yearExpression} desc")
            ->pluck('year')
            ->filter()
            ->values()
            ->all();

        $pendingRegistrationsCount = User::query()
            ->pending()
            ->count();

        return view('members.index', compact('members', 'wilayahField', 'wilayahOptions', 'yearOptions', 'pendingRegistrationsCount'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'kontak' => ['required', 'string', 'max:30'],
            'status' => ['required', 'in:aktif,tidak_aktif'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
        ]);

        Member::create($data);

        return redirect()->route('members.index')->with('success', 'Data jemaat berhasil ditambahkan.');
    }

    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'kontak' => ['required', 'string', 'max:30'],
            'status' => ['required', 'in:aktif,tidak_aktif'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
        ]);

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Data jemaat berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return back()->with('success', 'Data jemaat berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $wilayahField = collect(['wilayah', 'kelompok'])
            ->first(fn (string $column) => Schema::hasColumn('members', $column));

        return Excel::download(new MembersExport([
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'gender' => $request->string('gender')->toString(),
            'age_category' => $request->string('age_category')->toString(),
            'year' => $request->string('year')->toString(),
            'wilayah' => $request->string('wilayah')->toString(),
            'wilayah_field' => $wilayahField,
        ]), 'data-jemaat.xlsx');
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('members.pdf', [
            'members' => Member::query()->orderBy('nama')->get(),
        ]);

        return $pdf->download('data-jemaat.pdf');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,xls'],
        ]);

        Excel::import(new MembersImport, $request->file('file'));

        return back()->with('success', 'Import data jemaat berhasil.');
    }
}
