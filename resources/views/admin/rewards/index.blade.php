@extends('admin.layouts.app')
@section('title', 'Reward')
@section('heading', 'Manajemen Reward')
@section('subheading', 'Kelola item reward yang dapat ditukar pengguna dengan poin mereka.')

@section('content')
<div class="toolbar">
    <div class="text-sm muted">Total: {{ $rewards->total() }} reward</div>
    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary">🎁 Tambah Reward</a>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <table>
        <thead>
            <tr>
                <th style="padding-left:20px">Judul</th>
                <th>Biaya Poin</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rewards as $reward)
                <tr>
                    <td style="padding-left:20px">
                        <div class="font-bold">{{ $reward->title }}</div>
                        <div class="text-sm muted">{{ Str::limit($reward->description, 60) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-purple">🪙 {{ number_format($reward->points_cost) }} poin</span>
                    </td>
                    <td>
                        @if($reward->stock === null)
                            <span class="badge badge-blue">∞ Tidak terbatas</span>
                        @elseif($reward->stock > 0)
                            <span class="badge badge-neutral">{{ $reward->stock }}</span>
                        @else
                            <span class="badge badge-danger">Habis</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $reward->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $reward->is_active ? '✅ Aktif' : '⏸ Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn btn-sm" href="{{ route('admin.rewards.edit', $reward) }}">✏️ Edit</a>
                            <form method="POST" action="{{ route('admin.rewards.destroy', $reward) }}" onsubmit="return confirm('Hapus reward ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-2)">
                        <div style="font-size:36px;margin-bottom:8px">🎁</div>
                        Belum ada reward. Tambahkan reward pertama!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination" style="padding:16px 20px">{{ $rewards->links() }}</div>
</div>
@endsection
