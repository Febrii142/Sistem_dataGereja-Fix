<?php

namespace App\Http\Controllers;

use App\Models\Jemaat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JemaatRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit_own_jemaat');
    }

    public function showStep(int $step): View
    {
        abort_unless(in_array($step, [1, 2, 3], true), 404);

        return view("jemaat.registration.step{$step}", [
            'jemaat' => $this->getOrCreateCurrentJemaat()->load('baptisan'),
            'step' => $step,
        ]);
    }

    public function saveStep(Request $request, int $step): RedirectResponse
    {
        abort_unless(in_array($step, [1, 2, 3], true), 404);

        $jemaat = $this->getOrCreateCurrentJemaat();

        if ($step === 1) {
            $jemaat->update($request->validate([
                'nama_lengkap' => ['required', 'string', 'min:3', 'max:255'],
                'tempat_lahir' => ['required', 'string', 'max:255'],
                'tanggal_lahir' => ['required', 'date'],
                'no_telepon' => ['required', 'numeric'],
                'email' => ['required', 'email'],
            ]));

            return redirect()->route('jemaat.registration.show', ['step' => 2]);
        }

        if ($step === 2) {
            $jemaat->update($request->validate([
                'alamat' => ['required', 'string', 'min:5'],
                'kota' => ['required', 'string', 'max:255'],
                'kode_pos' => ['required', 'numeric'],
            ]));

            return redirect()->route('jemaat.registration.show', ['step' => 3]);
        }

        $data = $request->validate([
            'status_baptis' => ['required', 'in:sudah,belum'],
            'tanggal_baptis' => ['nullable', 'date', 'required_if:status_baptis,sudah'],
            'tempat_baptis' => ['nullable', 'string', 'required_if:status_baptis,sudah'],
            'nama_pendeta' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        $jemaat->update([
            'status_baptis' => $data['status_baptis'],
            'tanggal_baptis' => $data['status_baptis'] === 'sudah' ? $data['tanggal_baptis'] : null,
        ]);

        $jemaat->updateBaptisan($data['status_baptis'] === 'sudah'
            ? [
                'tanggal_baptis' => $data['tanggal_baptis'],
                'tempat_baptis' => $data['tempat_baptis'],
                'nama_pendeta' => $data['nama_pendeta'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]
            : null
        );

        return redirect()->route('jemaat.dashboard')->with('success', 'Pendaftaran jemaat berhasil dikirim.');
    }

    public function saveDraft(Request $request): RedirectResponse
    {
        $jemaat = $this->getOrCreateCurrentJemaat();

        $validated = $request->validate([
            'nama_lengkap' => ['sometimes', 'string', 'min:3', 'max:255'],
            'tempat_lahir' => ['sometimes', 'string', 'max:255'],
            'tanggal_lahir' => ['sometimes', 'date'],
            'no_telepon' => ['sometimes', 'numeric'],
            'email' => ['sometimes', 'email'],
            'alamat' => ['sometimes', 'string', 'min:5'],
            'kota' => ['sometimes', 'string', 'max:255'],
            'kode_pos' => ['sometimes', 'numeric'],
            'status_baptis' => ['sometimes', 'in:sudah,belum'],
            'tanggal_baptis' => ['sometimes', 'nullable', 'date'],
            'tempat_baptis' => ['sometimes', 'nullable', 'string'],
            'nama_pendeta' => ['sometimes', 'nullable', 'string', 'max:255'],
            'catatan' => ['sometimes', 'nullable', 'string'],
        ]);

        $jemaat->fill($validated);

        if (($validated['status_baptis'] ?? $jemaat->status_baptis) === 'belum') {
            $jemaat->tanggal_baptis = null;
        }

        $jemaat->save();

        return back()->with('success', 'Draft pendaftaran berhasil disimpan.');
    }

    public function editProfile(): RedirectResponse
    {
        return redirect()->route('jemaat.profile');
    }

    public function showStep1(): View
    {
        return $this->showStep(1);
    }

    public function saveStep1(Request $request): RedirectResponse
    {
        return $this->saveStep($request, 1);
    }

    public function showStep2(): View
    {
        return $this->showStep(2);
    }

    public function saveStep2(Request $request): RedirectResponse
    {
        return $this->saveStep($request, 2);
    }

    public function showStep3(): View
    {
        return $this->showStep(3);
    }

    public function saveStep3(Request $request): RedirectResponse
    {
        return $this->saveStep($request, 3);
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
