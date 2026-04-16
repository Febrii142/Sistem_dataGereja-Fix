<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Category;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->orderBy('type')->orderBy('name')->get(['id', 'name', 'type']);

        $members = Member::query()
            ->with('categories:id,name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('nama', 'like', '%'.$request->string('search').'%')
                        ->orWhere('kontak', 'like', '%'.$request->string('search').'%');
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('category_id'), fn ($query) => $query->whereHas('categories', fn ($subQuery) => $subQuery->whereKey($request->integer('category_id'))))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members', 'categories'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('type')->orderBy('name')->get(['id', 'name', 'type']);

        return view('members.create', compact('categories'));
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
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $member = Member::create(collect($data)->except('category_ids')->all());
        $member->categories()->sync($data['category_ids'] ?? []);

        return redirect()->route('members.index')->with('success', 'Data jemaat berhasil ditambahkan.');
    }

    public function show(Member $member)
    {
        $member->load('categories:id,name,type');

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $categories = Category::query()->orderBy('type')->orderBy('name')->get(['id', 'name', 'type']);
        $member->load('categories:id');

        return view('members.edit', compact('member', 'categories'));
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
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $member->update(collect($data)->except('category_ids')->all());
        $member->categories()->sync($data['category_ids'] ?? []);

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
