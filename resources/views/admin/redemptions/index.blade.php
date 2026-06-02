@extends('admin.layouts.app')
@section('title', 'Penukaran Reward')
@section('heading', 'Manajemen Penukaran Reward')
@section('subheading', 'Proses penukaran item reward yang diajukan oleh pengguna.')

@push('styles')
<style>
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tab {
        padding: 7px 16px;
        border-radius: 999px;
        border: 1.5px solid var(--line);
        background: var(--card);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-2);
        cursor: pointer;
        transition: all .15s;
    }
    .filter-tab:hover { border-color: var(--primary); color: var(--primary); }
    .filter-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

    .user-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 12px; color: #fff;
        flex-shrink: 0;
    }
    .tbl-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    
    .action-btn-group {
        display: flex;
        gap: 6px;
    }
</style>
@endpush

@section('content')
<div class="toolbar" style="margin-bottom:4px">
    <div class="filter-tabs">
        <a class="filter-tab {{ $status === null ? 'active' : '' }}" href="{{ route('admin.redemptions.index') }}">Semua</a>
        <a class="filter-tab {{ $status === 'Menunggu proses' ? 'active' : '' }}" href="{{ route('admin.redemptions.index', ['status' => 'Menunggu proses']) }}">⏳ Menunggu</a>
        <a class="filter-tab {{ $status === 'Diproses' ? 'active' : '' }}" href="{{ route('admin.redemptions.index', ['status' => 'Diproses']) }}">🔄 Diproses</a>
        <a class="filter-tab {{ $status === 'Selesai' ? 'active' : '' }}" href="{{ route('admin.redemptions.index', ['status' => 'Selesai']) }}">✅ Selesai</a>
        <a class="filter-tab {{ $status === 'Ditolak' ? 'active' : '' }}" href="{{ route('admin.redemptions.index', ['status' => 'Ditolak']) }}">❌ Ditolak</a>
    </div>
    <div class="text-sm muted">{{ $redemptions->total() }} penukaran</div>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <div class="tbl-scroll">
        <table style="min-width: 800px">
            <thead>
                <tr>
                    <th style="padding-left:20px">Pengguna</th>
                    <th>Reward</th>
                    <th>Biaya Poin</th>
                    <th>Waktu Pengajuan</th>
                    <th>Status</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($redemptions as $redemption)
                    <tr>
                        <td style="padding-left:20px">
                            <div style="display:flex;align-items:center;gap:10px">
                                @php 
                                    $colors = ['#16a34a','#0ea5e9','#8b5cf6','#f97316','#dc2626','#0d9488']; 
                                    $c = $colors[crc32($redemption->user?->display_name ?? 'X') % count($colors)]; 
                                @endphp
                                <div class="user-avatar" style="background:{{ $c }}">{{ strtoupper(substr($redemption->user?->display_name ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div class="font-bold">{{ $redemption->user?->display_name ?? '-' }}</div>
                                    <div class="text-sm muted">{{ $redemption->user?->username ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-bold">{{ $redemption->reward?->title ?? 'Reward Terhapus' }}</div>
                            <div class="text-sm muted">{{ Str::limit($redemption->reward?->description ?? '', 40) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-purple">🪙 {{ number_format($redemption->points_spent) }} poin</span>
                        </td>
                        <td class="text-sm muted">
                            {{ optional($redemption->redeemed_at)->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge {{ $redemption->status === 'Selesai' ? 'badge-success' : ($redemption->status === 'Diproses' ? 'badge-warning' : ($redemption->status === 'Ditolak' ? 'badge-danger' : 'badge-neutral')) }}">
                                {{ $redemption->status }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($redemption->status, ['Menunggu proses', 'Diproses']))
                                <div class="action-btn-group">
                                    @if($redemption->status === 'Menunggu proses')
                                        <form method="POST" action="{{ route('admin.redemptions.status', $redemption) }}" style="margin:0;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="Diproses">
                                            <button type="submit" class="btn btn-sm btn-primary" style="background:#0ea5e9; border-color:#0ea5e9;">🔄 Proses</button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.redemptions.status', $redemption) }}" style="margin:0;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Selesai">
                                        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Selesaikan penukaran reward ini?')">✅ Selesai</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.redemptions.status', $redemption) }}" style="margin:0;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Ditolak">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak penukaran ini? Poin akan dikembalikan otomatis ke saldo pengguna.')">❌ Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm muted">Sudah Final</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-2)">
                            <div style="font-size:36px;margin-bottom:8px">💸</div>
                            Tidak ada pengajuan penukaran reward.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination" style="padding:16px 20px">
        {{ $redemptions->links() }}
    </div>
</div>
@endsection
