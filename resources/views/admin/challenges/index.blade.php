@extends('admin.layouts.app')
@section('title', 'Challenge')
@section('heading', 'Manajemen Challenge')
@section('subheading', 'Kelola challenge aktif untuk meningkatkan keterlibatan pengguna.')

@section('content')
<div class="toolbar">
    <div class="text-sm muted">Total: {{ $challenges->total() }} challenge</div>
    <a href="{{ route('admin.challenges.create') }}" class="btn btn-primary">🏆 Tambah Challenge</a>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th style="padding-left:20px">Judul & Deskripsi</th>
                    <th>Target</th>
                    <th>Reward Poin</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($challenges as $challenge)
                    <tr>
                        <td style="padding-left:20px">
                            <div class="font-bold">{{ $challenge->title }}</div>
                            <div class="text-sm muted">{{ Str::limit($challenge->description, 60) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-blue">🎯 {{ $challenge->target }} scan</span>
                        </td>
                        <td>
                            <span class="badge badge-purple">🪙 {{ number_format($challenge->reward_points) }}</span>
                        </td>
                        <td class="text-sm muted">
                            <div>{{ optional($challenge->starts_at)->format('d/m/Y') }}</div>
                            <div>— {{ optional($challenge->ends_at)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            @php
                                $now = now();
                                $isActive = $challenge->is_active && $challenge->starts_at <= $now && $challenge->ends_at >= $now;
                            @endphp
                            <span class="badge {{ $isActive ? 'badge-success' : ($challenge->is_active ? 'badge-warning' : 'badge-neutral') }}">
                                {{ $isActive ? '✅ Aktif' : ($challenge->is_active ? '⏳ Menunggu' : '⏸ Nonaktif') }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-sm" href="{{ route('admin.challenges.edit', $challenge) }}">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.challenges.destroy', $challenge) }}" onsubmit="return confirm('Hapus challenge ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-2)">
                            <div style="font-size:36px;margin-bottom:8px">🏆</div>
                            Belum ada challenge. Tambahkan challenge pertama!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination" style="padding:16px 20px">{{ $challenges->links() }}</div>
</div>
@endsection
