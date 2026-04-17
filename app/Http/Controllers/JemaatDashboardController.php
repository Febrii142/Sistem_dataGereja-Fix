<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Jemaat;
use App\Models\KategoriJemaat;
use App\Models\KeluargaJemaat;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JemaatDashboardController extends Controller
{
    private const DEFAULT_AGE_YEARS = 18;

    public function dashboard(): View
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        $memberIds = Member::query()
            ->where(function ($query) use ($jemaat) {
                $query->where('nama', $jemaat->nama_lengkap)
                    ->orWhere('kontak', $jemaat->no_telepon);
            })
            ->pluck('id');

        $attendanceCount = Attendance::query()
            ->whereIn('member_id', $memberIds)
            ->where('hadir', true)
            ->count();

        return view('jemaat.dashboard', [
            'jemaat' => $jemaat,
            'familyCount' => KeluargaJemaat::query()->where('jemaat_id', $jemaat->id)->count(),
            'attendanceCount' => $attendanceCount,
            'upcomingEvents' => Attendance::query()
                ->whereIn('member_id', $memberIds)
                ->select('service_date')
                ->whereDate('service_date', '>=', now()->toDateString())
                ->orderBy('service_date')
                ->limit(3)
                ->get()
                ->map(fn ($attendance) => [
                    'title' => 'Ibadah Jemaat',
                    'date' => $attendance->service_date,
                ]),
        ]);
    }

    public function profile(): View
    {
        return view('jemaat.profile', [
            'jemaat' => $this->getOrCreateCurrentJemaat(),
            'kategoriOptions' => KategoriJemaat::query()->orderBy('nama')->pluck('nama'),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_telp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'min:5'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'status_perkawinan' => ['required', 'in:Belum Menikah,Menikah,Janda,Duda'],
            'kategori_jemaat' => ['nullable', 'string', 'max:255'],
        ]);

        $jemaat = $this->getOrCreateCurrentJemaat();
        $jemaat->update([
            'nama_lengkap' => $validated['nama'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telp'],
            'alamat' => $validated['alamat'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'status_perkawinan' => $validated['status_perkawinan'],
            'kategori_jemaat' => $validated['kategori_jemaat'] ?: null,
        ]);

        return redirect()->route('jemaat.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function family(): View
    {
        $jemaat = $this->getOrCreateCurrentJemaat();

        return view('jemaat.family', [
            'jemaat' => $jemaat,
            'familyMembers' => KeluargaJemaat::query()
                ->where('jemaat_id', $jemaat->id)
                ->orderBy('hubungan')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function storeFamily(Request $request): RedirectResponse
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        KeluargaJemaat::query()->create([
            ...$request->validate([
                'nama' => ['required', 'string', 'min:3', 'max:255'],
                'hubungan' => ['required', 'string', 'max:100'],
                'no_telp' => ['nullable', 'string', 'max:30'],
                'tanggal_lahir' => ['nullable', 'date'],
            ]),
            'jemaat_id' => $jemaat->id,
        ]);

        return redirect()->route('jemaat.family')->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    public function updateFamily(Request $request, int $id): RedirectResponse
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        $family = KeluargaJemaat::query()
            ->where('jemaat_id', $jemaat->id)
            ->findOrFail($id);

        $family->update($request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:255'],
            'hubungan' => ['required', 'string', 'max:100'],
            'no_telp' => ['nullable', 'string', 'max:30'],
            'tanggal_lahir' => ['nullable', 'date'],
        ]));

        return redirect()->route('jemaat.family')->with('success', 'Anggota keluarga berhasil diperbarui.');
    }

    public function deleteFamily(int $id): RedirectResponse
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        KeluargaJemaat::query()
            ->where('jemaat_id', $jemaat->id)
            ->findOrFail($id)
            ->delete();

        return redirect()->route('jemaat.family')->with('success', 'Anggota keluarga berhasil dihapus.');
    }

    private function getOrCreateCurrentJemaat(): Jemaat
    {
        $user = auth()->user();

        return Jemaat::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'tempat_lahir' => '-',
                'tanggal_lahir' => now()->subYears(self::DEFAULT_AGE_YEARS)->toDateString(),
                'no_telepon' => '-',
                'email' => $user->email,
                'status_perkawinan' => 'Belum Menikah',
                'kategori_jemaat' => 'Umum',
            ]
        );
    }
}
