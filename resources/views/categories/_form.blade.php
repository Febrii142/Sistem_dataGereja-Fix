@csrf
<div class="grid gap-4 md:grid-cols-2">
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" placeholder="Nama kategori" class="rounded border px-3 py-2" required>
    <select name="type" class="rounded border px-3 py-2" required>
        <option value="umur" @selected(old('type', $category->type ?? '')==='umur')>Umur</option>
        <option value="status" @selected(old('type', $category->type ?? '')==='status')>Status</option>
        <option value="wilayah" @selected(old('type', $category->type ?? '')==='wilayah')>Wilayah/Kelompok</option>
    </select>
    <input type="number" min="0" max="120" name="min_age" value="{{ old('min_age', $category->min_age ?? '') }}" placeholder="Umur minimum (opsional)" class="rounded border px-3 py-2">
    <input type="number" min="0" max="120" name="max_age" value="{{ old('max_age', $category->max_age ?? '') }}" placeholder="Umur maksimum (opsional)" class="rounded border px-3 py-2">
    <textarea name="description" placeholder="Deskripsi (opsional)" class="md:col-span-2 rounded border px-3 py-2">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<button class="mt-4 rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
