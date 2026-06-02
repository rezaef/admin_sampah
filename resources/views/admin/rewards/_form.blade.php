<div class="field"><label>Judul</label><input type="text" name="title" value="{{ old('title', $reward->title ?? '') }}" required></div>
<div class="field"><label>Deskripsi</label><textarea name="description" required>{{ old('description', $reward->description ?? '') }}</textarea></div>
<div class="grid-2">
    <div class="field"><label>Biaya Poin</label><input type="number" name="points_cost" value="{{ old('points_cost', $reward->points_cost ?? 0) }}" min="0" required></div>
    <div class="field"><label>Stok</label><input type="number" name="stock" value="{{ old('stock', $reward->stock ?? '') }}" min="0"></div>
</div>
<div class="field"><label>URL Gambar</label><input type="url" name="image_url" value="{{ old('image_url', $reward->image_url ?? '') }}"></div>
<div class="field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $reward->is_active ?? false))> Aktif</label></div>
<div class="actions"><button class="btn btn-primary" type="submit">Simpan</button><a class="btn" href="{{ route('admin.rewards.index') }}">Kembali</a></div>
