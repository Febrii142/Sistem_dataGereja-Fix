@csrf
<div class="grid gap-4 md:grid-cols-2">
    <input type="text" name="nama" value="{{ old('nama', $member->nama ?? '') }}" placeholder="Nama" class="rounded border px-3 py-2" required>
    <input type="text" name="kontak" value="{{ old('kontak', $member->kontak ?? '') }}" placeholder="Kontak" class="rounded border px-3 py-2" required>
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', isset($member)?$member->tanggal_lahir:'') }}" class="rounded border px-3 py-2" required>
    <select name="jenis_kelamin" class="rounded border px-3 py-2" required>
        <option value="L" @selected(old('jenis_kelamin', $member->jenis_kelamin ?? '')==='L')>Laki-laki</option>
        <option value="P" @selected(old('jenis_kelamin', $member->jenis_kelamin ?? '')==='P')>Perempuan</option>
    </select>
    <select name="status" class="rounded border px-3 py-2" required>
        <option value="aktif" @selected(old('status', $member->status ?? '')==='aktif')>Aktif</option>
        <option value="tidak_aktif" @selected(old('status', $member->status ?? '')==='tidak_aktif')>Tidak Aktif</option>
    </select>
    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $member->pekerjaan ?? '') }}" placeholder="Pekerjaan" class="rounded border px-3 py-2">
    <textarea name="alamat" placeholder="Alamat" class="md:col-span-2 rounded border px-3 py-2" required>{{ old('alamat', $member->alamat ?? '') }}</textarea>
</div>
<button class="mt-4 rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
