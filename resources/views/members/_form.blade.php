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
    <div class="md:col-span-2 rounded border p-3">
        <p class="mb-2 text-sm font-semibold text-slate-700">Kategori Jemaat (bisa lebih dari satu)</p>
        @php
            $selectedCategories = collect(old('category_ids', isset($member) ? $member->categories->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
        @endphp
        <div class="grid gap-2 md:grid-cols-2">
            @forelse($categories as $category)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        @checked(in_array($category->id, $selectedCategories, true))
                    >
                    <span>{{ $category->name }} ({{ ucfirst($category->type) }})</span>
                </label>
            @empty
                <p class="text-sm text-slate-500">Belum ada kategori. Tambahkan dari halaman Daftar Jemaat.</p>
            @endforelse
        </div>
    </div>
</div>
<button class="mt-4 rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
