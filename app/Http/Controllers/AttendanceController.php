<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::query()
            ->with('member')
            ->when($request->filled('member_id'), fn ($query) => $query->where('member_id', $request->integer('member_id')))
            ->when($request->filled('service_date'), fn ($query) => $query->whereDate('service_date', $request->date('service_date')))
            ->latest('service_date')
            ->paginate(10)
            ->withQueryString();

        $members = Member::query()->orderBy('nama')->get();

        $stats = [
            'hadir' => Attendance::where('hadir', true)->count(),
            'tidak_hadir' => Attendance::where('hadir', false)->count(),
            'total' => Attendance::count(),
        ];

        return view('attendances.index', compact('attendances', 'members', 'stats'));
    }

    public function create()
    {
        return view('attendances.create', [
            'members' => Member::query()->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'service_date' => ['required', 'date'],
            'hadir' => ['required', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        Attendance::updateOrCreate(
            ['member_id' => $data['member_id'], 'service_date' => $data['service_date']],
            $data
        );

        return redirect()->route('attendances.index')->with('success', 'Kehadiran berhasil disimpan.');
    }

    public function edit(Attendance $attendance)
    {
        return view('attendances.edit', [
            'attendance' => $attendance,
            'members' => Member::query()->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'service_date' => ['required', 'date'],
            'hadir' => ['required', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $attendance->update($data);

        return redirect()->route('attendances.index')->with('success', 'Kehadiran diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Data kehadiran dihapus.');
    }
}
