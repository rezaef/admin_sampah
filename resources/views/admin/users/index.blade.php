@extends('admin.layouts.app')
@section('title', 'Pengguna')
@section('heading', 'Daftar Pengguna')
@section('subheading', 'Akun user dan admin yang terdaftar pada sistem.')

@push('styles')
<style>
    .user-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px; color: #fff;
        flex-shrink: 0;
    }
    .points-bar-wrap { background: rgba(148,163,184,0.1); border-radius: 999px; height: 5px; width: 70px; flex-shrink: 0; }
    .points-bar { height: 100%; border-radius: 999px; background: linear-gradient(90deg,#16a34a,#4ade80); max-width: 100%; }
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
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
        text-decoration: none;
    }
    .filter-tab:hover { border-color: var(--primary); color: var(--primary); }
    .filter-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
</style>
@endpush

@section('content')
<div class="toolbar" style="margin-bottom:16px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
    <div class="filter-tabs">
        <a class="filter-tab {{ request('role') === null ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except(['role', 'page']), ['role' => null])) }}">Semua</a>
        <a class="filter-tab {{ request('role') === 'user' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except(['role', 'page']), ['role' => 'user'])) }}">👤 User</a>
        <a class="filter-tab {{ request('role') === 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except(['role', 'page']), ['role' => 'admin'])) }}">🛡️ Admin</a>
    </div>
    
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        @if(request('role'))
            <input type="hidden" name="role" value="{{ request('role') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." style="padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--line-strong); background: var(--input-bg); color: var(--text); font-size:12.5px; width:180px;">
        <select name="sort" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--line-strong); background: var(--input-bg); color: var(--text); font-size:12.5px; outline:none; cursor:pointer;">
            <option value="newest" @selected(request('sort') === 'newest')>Terbaru</option>
            <option value="points" @selected(request('sort') === 'points')>Poin Terbanyak</option>
            <option value="scans" @selected(request('sort') === 'scans')>Scan Terbanyak</option>
            <option value="reports" @selected(request('sort') === 'reports')>Laporan Terbanyak</option>
        </select>
        <button type="submit" class="btn btn-sm" style="padding:7px 12px; background:var(--card); border:1px solid var(--line-strong);">🔍</button>
        @if(request('search') || request('sort') && request('sort') !== 'newest')
            <a href="{{ route('admin.users.index', request('role') ? ['role' => request('role')] : []) }}" class="btn btn-sm" style="color:var(--text-3); padding:7px 12px; background:var(--card); border:1px solid var(--line-strong);">Reset</a>
        @endif
    </form>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:18px 20px 0;display:flex;justify-content:space-between;align-items:center">
        <div>
            <div class="card-title">Pengguna Terdaftar</div>
            <div class="card-sub text-sm muted">Total: <strong id="users-total">{{ $users->total() }}</strong></div>
        </div>
    </div>
    <div class="tbl-scroll">
        <table style="margin-top:12px;min-width:900px">
            <thead>
                <tr>
                    <th style="padding-left:20px;min-width:200px">Pengguna</th>
                    <th style="min-width:130px">Username</th>
                    <th style="min-width:200px">Email</th>
                    <th style="min-width:90px">Role</th>
                    <th style="min-width:140px">Poin</th>
                    <th style="min-width:60px">Scan</th>
                    <th style="min-width:80px">Laporan</th>
                    <th style="width:160px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="padding-left:20px">
                            <div style="display:flex;align-items:center;gap:10px">
                                @php
                                    $colors = ['#16a34a','#0ea5e9','#8b5cf6','#f97316','#dc2626','#0d9488','#7c3aed','#db2777'];
                                    $c = $colors[crc32($user->display_name) % count($colors)];
                                @endphp
                                <div class="user-avatar" style="background:{{ $c }}">{{ strtoupper(substr($user->display_name, 0, 1)) }}</div>
                                <div class="font-bold">{{ $user->display_name }}</div>
                            </div>
                        </td>
                        <td class="text-sm muted">{{ $user->username }}</td>
                        <td class="text-sm muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-warning' : 'badge-neutral' }}">
                                {{ $user->role === 'admin' ? '🛡️' : '👤' }} {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="font-bold">{{ number_format($user->points_balance) }}</span>
                                <div class="points-bar-wrap">
                                    @php $maxPts = max(1, $users->max('points_balance')); @endphp
                                    <div class="points-bar" style="width:{{ min(100, ($user->points_balance / $maxPts) * 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $user->classifications_count }}</span>
                        </td>
                        <td>
                            <span class="badge badge-neutral">{{ $user->reports_count }}</span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <button class="btn btn-sm" onclick="openEditModal({{ json_encode($user) }})" style="padding: 5px 10px; background:var(--card); border: 1px solid var(--line-strong);">✏️ Edit</button>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua riwayat deteksi dan laporan pengguna ini akan ikut terhapus.')" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 5px 10px;">🗑️ Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-2)">
                            <div style="font-size:36px;margin-bottom:8px">👥</div>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination" style="padding:16px 20px">{{ $users->links() }}</div>
</div>

{{-- Modal Edit Pengguna --}}
<div id="edit-user-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);">
    <div class="card" style="width:100%; max-width:420px; margin:20px; padding:24px; position:relative; border: 1.5px solid var(--glass-border); background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow-lg);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:12px;">
            <div class="card-title" style="margin:0;">Edit Pengguna</div>
            <span style="font-size:24px; cursor:pointer; line-height:1; color:var(--text-2);" onclick="closeEditModal()">×</span>
        </div>
        
        <form id="edit-user-form" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div class="field" style="margin-bottom:16px;">
                <label class="font-bold" style="display:block; margin-bottom:6px; font-size:11px; color:var(--text-2); text-transform:uppercase; letter-spacing:0.5px;">Nama Pengguna</label>
                <input type="text" id="modal-display-name" disabled style="width:100%; padding:10px 12px; border-radius:var(--radius-sm); border:1px solid var(--line-strong); background:rgba(148,163,184,0.06); color:var(--text-3); font-size:13.5px;">
            </div>
            
            <div class="field" style="margin-bottom:16px;">
                <label class="font-bold" style="display:block; margin-bottom:6px; font-size:11px; color:var(--text-2); text-transform:uppercase; letter-spacing:0.5px;">Saldo Poin</label>
                <input type="number" name="points_balance" id="modal-points" required min="0" style="width:100%; padding:10px 12px; border-radius:var(--radius-sm); border:1px solid var(--line-strong); background:var(--input-bg); color:var(--text); font-size:13.5px; outline:none;">
            </div>
            
            <div class="field" style="margin-bottom:24px;">
                <label class="font-bold" style="display:block; margin-bottom:6px; font-size:11px; color:var(--text-2); text-transform:uppercase; letter-spacing:0.5px;">Hak Akses / Role</label>
                <select name="role" id="modal-role" required style="width:100%; padding:10px 12px; border-radius:var(--radius-sm); border:1px solid var(--line-strong); background:var(--input-bg); color:var(--text); font-size:13.5px; outline:none; cursor:pointer;">
                    <option value="user">👤 User (Pengguna Mobile)</option>
                    <option value="admin">🛡️ Admin (Akses Panel Web)</option>
                </select>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" onclick="closeEditModal()" style="background:var(--card); border:1px solid var(--line-strong);">Batal</button>
                <button type="submit" class="btn btn-primary" style="background:var(--primary); border-color:var(--primary); color:#fff;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(user) {
    const modal = document.getElementById('edit-user-modal');
    const form = document.getElementById('edit-user-form');
    
    form.action = `/admin/users/${user.id}`;
    document.getElementById('modal-display-name').value = user.display_name;
    document.getElementById('modal-points').value = user.points_balance;
    document.getElementById('modal-role').value = user.role;
    
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-user-modal').style.display = 'none';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEditModal();
});
</script>
@endpush
