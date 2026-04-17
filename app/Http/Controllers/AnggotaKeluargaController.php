<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKeluarga;
use App\Models\Jemaat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaKeluargaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_own_family')->only(['index']);
        $this->middleware('permission:manage_family_members')->except(['index']);
    }

    public function index(): View
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        $kepalaId = $jemaat->isKepalaKeluarga() ? $jemaat->id : $jemaat->kepala_keluarga_id;

        return view('jemaat.keluarga.index', [
            'jemaat' => $jemaat,
            'canManage' => $jemaat->isKepalaKeluarga(),
            'anggotaKeluarga' => AnggotaKeluarga::query()
                ->with('jemaat')
                ->where('kepala_keluarga_id', $kepalaId)
                ->orderBy('hubungan_keluarga')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $jemaat = $this->getOrCreateCurrentJemaat();
        abort_unless($jemaat->isKepalaKeluarga(), 403);

        return view('jemaat.keluarga.create', [
            'jemaat' => $jemaat,
            'jemaatExisting' => Jemaat::query()
                ->where('id', '!=', $jemaat->id)
                ->orderBy('nama_lengkap')
                ->limit(100)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $kepala = $this->getOrCreateCurrentJemaat();
        abort_unless($kepala->isKepalaKeluarga(), 403);

        $data = $request->validate([
            'mode' => ['required', 'in:new,existing'],
            'hubungan_keluarga' => ['required', 'in:Istri,Suami,Anak,Orangtua,Saudara'],
            'status' => ['required', 'in:aktif,non-aktif'],
            'existing_jemaat_id' => ['nullable', 'required_if:mode,existing', 'exists:jemaat,id'],
            'nama_lengkap' => ['nullable', 'required_if:mode,new', 'string', 'min:3', 'max:255'],
            'tempat_lahir' => ['nullable', 'required_if:mode,new', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'required_if:mode,new', 'date'],
            'no_telepon' => ['nullable', 'required_if:mode,new', 'numeric'],
            'email' => ['nullable', 'required_if:mode,new', 'email'],
        ]);

        $jemaatId = $data['mode'] === 'existing'
            ? (int) $data['existing_jemaat_id']
            : Jemaat::query()->create([
                'nama_lengkap' => $data['nama_lengkap'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'no_telepon' => $data['no_telepon'],
                'email' => $data['email'],
                'status_baptis' => 'belum',
                'kepala_keluarga_id' => $kepala->id,
            ])->id;

        $kepala->addAnggotaKeluarga([
            'jemaat_id' => $jemaatId,
            'hubungan_keluarga' => $data['hubungan_keluarga'],
            'status' => $data['status'],
        ]);

        Jemaat::query()->whereKey($jemaatId)->update(['kepala_keluarga_id' => $kepala->id]);

        return redirect()->route('jemaat.keluarga.index')->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $kepala = $this->getOrCreateCurrentJemaat();
        abort_unless($kepala->isKepalaKeluarga(), 403);

        return view('jemaat.keluarga.edit', [
            'anggota' => AnggotaKeluarga::query()
                ->with('jemaat')
                ->where('kepala_keluarga_id', $kepala->id)
                ->findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kepala = $this->getOrCreateCurrentJemaat();
        abort_unless($kepala->isKepalaKeluarga(), 403);

        $anggota = AnggotaKeluarga::query()
            ->where('kepala_keluarga_id', $kepala->id)
            ->findOrFail($id);

        $anggota->update($request->validate([
            'hubungan_keluarga' => ['required', 'in:Istri,Suami,Anak,Orangtua,Saudara'],
            'status' => ['required', 'in:aktif,non-aktif'],
        ]));

        return redirect()->route('jemaat.keluarga.index')->with('success', 'Data anggota keluarga berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kepala = $this->getOrCreateCurrentJemaat();
        abort_unless($kepala->isKepalaKeluarga(), 403);

        $anggota = AnggotaKeluarga::query()
            ->where('kepala_keluarga_id', $kepala->id)
            ->findOrFail($id);

        Jemaat::query()->whereKey($anggota->jemaat_id)->update(['kepala_keluarga_id' => null]);
        $anggota->delete();

        return back()->with('success', 'Anggota keluarga berhasil dihapus.');
    }

    private function getOrCreateCurrentJemaat(): Jemaat
    {
        $user = auth()->user();

        return Jemaat::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'tempat_lahir' => '-',
                'tanggal_lahir' => now()->subYears(18)->toDateString(),
                'no_telepon' => '0',
                'email' => $user->email,
            ]
        );
    }
}
