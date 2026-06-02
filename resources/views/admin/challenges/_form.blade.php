<div class="field"><label>Judul</label><input type="text" name="title" value="{{ old('title', $challenge->title ?? '') }}" required></div>
<div class="field"><label>Deskripsi</label><textarea name="description" required>{{ old('description', $challenge->description ?? '') }}</textarea></div>
<div class="grid-2">
    <div class="field"><label>Target</label><input type="number" name="target" value="{{ old('target', $challenge->target ?? 1) }}" min="1" required></div>
    <div class="field"><label>Reward Poin</label><input type="number" name="reward_points" value="{{ old('reward_points', $challenge->reward_points ?? 0) }}" min="0" required></div>
</div>
<div class="grid-2">
    <div class="field"><label>Mulai</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($challenge) && $challenge->starts_at ? $challenge->starts_at->format('Y-m-d\\TH:i') : '') }}" required></div>
    <div class="field"><label>Selesai</label><input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($challenge) && $challenge->ends_at ? $challenge->ends_at->format('Y-m-d\\TH:i') : '') }}" required></div>
</div>
<div class="field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $challenge->is_active ?? false))> Aktif</label></div>
<div class="actions"><button class="btn btn-primary" type="submit">Simpan</button><a class="btn" href="{{ route('admin.challenges.index') }}">Kembali</a></div>
