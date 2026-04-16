<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nama', 'like', '%'.$request->string('search').'%')
                    ->orWhere('kontak', 'like', '%'.$request->string('search').'%');
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members'));
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

    public function exportExcel()
    {
        return Excel::download(new MembersExport(), 'data-jemaat.xlsx');
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

        Excel::import(new MembersImport(), $request->file('file'));

        return back()->with('success', 'Import data jemaat berhasil.');
    }
}
