<?php

namespace App\Http\Controllers;

use App\Exports\CategoryMembersExport;
use App\Models\Category;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->withCount('members')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $statsByType = Category::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('categories.index', compact('categories', 'statsByType'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:umur,status,wilayah'],
            'description' => ['nullable', 'string'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:120', 'gte:min_age'],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Request $request, Category $category)
    {
        $members = $category->members()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('nama', 'like', '%'.$request->string('search').'%')
                        ->orWhere('kontak', 'like', '%'.$request->string('search').'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalMembers = $category->members()->count();
        $maleMembers = $category->members()->where('jenis_kelamin', 'L')->count();
        $femaleMembers = $category->members()->where('jenis_kelamin', 'P')->count();
        $activeMembers = $category->members()->where('status', 'aktif')->count();

        $ageAverage = null;
        if ($totalMembers > 0) {
            $rawAverage = $category->members()
                ->whereNotNull('tanggal_lahir')
                ->get(['tanggal_lahir'])
                ->map(fn ($member) => Carbon::parse($member->tanggal_lahir)->age)
                ->avg();
            $ageAverage = $rawAverage !== null ? round($rawAverage, 1) : null;
        }

        return view('categories.show', compact(
            'category',
            'members',
            'totalMembers',
            'maleMembers',
            'femaleMembers',
            'activeMembers',
            'ageAverage'
        ));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:umur,status,wilayah'],
            'description' => ['nullable', 'string'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:120', 'gte:min_age'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function exportExcel(Category $category)
    {
        return Excel::download(new CategoryMembersExport($category), 'kategori-'.$category->id.'-jemaat.xlsx');
    }
}
