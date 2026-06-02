@extends('admin.layouts.app')
@section('title', 'Klasifikasi')
@section('heading', 'Riwayat Klasifikasi')
@section('subheading', 'Seluruh hasil scan sampah dari pengguna aplikasi mobile.')

@push('styles')
<style>
    .confidence-bar-wrap { background: var(--line); border-radius: 999px; height: 7px; flex: 1; min-width: 80px; }
    .confidence-bar { height: 100%; border-radius: 999px; transition: width .6s ease; }
    .confidence-bar.high { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .confidence-bar.med { background: linear-gradient(90deg,#ca8a04,#facc15); }
    .confidence-bar.low { background: linear-gradient(90deg,#dc2626,#f87171); }
    .user-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 12px; color: #fff;
        flex-shrink: 0;
    }
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
<div class="toolbar" style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
    <div class="filter-tabs">
        <a class="filter-tab {{ request('category') === null ? 'active' : '' }}" href="{{ route('admin.classifications.index') }}">Semua</a>
        <a class="filter-tab {{ request('category') === 'organik' ? 'active' : '' }}" href="{{ route('admin.classifications.index', ['category' => 'organik']) }}">🌱 Organik</a>
        <a class="filter-tab {{ request('category') === 'anorganik' ? 'active' : '' }}" href="{{ route('admin.classifications.index', ['category' => 'anorganik']) }}">♻️ Anorganik</a>
        <a class="filter-tab {{ request('category') === 'tidak_diketahui' ? 'active' : '' }}" href="{{ route('admin.classifications.index', ['category' => 'tidak_diketahui']) }}">❓ Lainnya</a>
    </div>
    <div class="text-sm muted">{{ $classifications->total() }} data</div>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:18px 20px 0;display:flex;justify-content:space-between;align-items:center">
        <div>
            <div class="card-title">Klasifikasi Sampah</div>
            <div class="card-sub text-sm muted">Total: <strong id="cls-total">{{ $classifications->total() }}</strong></div>
        </div>
        <span class="badge badge-blue" id="cls-live">🔄 Auto-refresh 30s</span>
    </div>

    <table style="margin-top:12px">
        <thead>
            <tr>
                <th style="padding-left:20px">Pengguna</th>
                <th>Kategori</th>
                <th style="min-width:180px">Confidence</th>
                <th>Engine</th>
                <th>Latency</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody id="cls-tbody">
            @forelse($classifications as $item)
                <tr>
                    <td style="padding-left:20px">
                        <div style="display:flex;align-items:center;gap:10px">
                            @php $colors = ['#16a34a','#0ea5e9','#8b5cf6','#f97316','#dc2626','#0d9488']; $c = $colors[crc32($item->user?->display_name ?? 'X') % count($colors)]; @endphp
                            <div class="user-avatar" style="background:{{ $c }}">{{ strtoupper(substr($item->user?->display_name ?? '?', 0, 1)) }}</div>
                            <div>
                                <div class="font-bold">{{ $item->user?->display_name ?? '-' }}</div>
                                <div class="text-sm muted">{{ $item->user?->username ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php $isOrganic = str_contains(strtolower($item->category),'organik') && !str_contains(strtolower($item->category),'an'); @endphp
                        <span class="badge {{ $isOrganic ? 'badge-success' : 'badge-blue' }}">
                            {{ $isOrganic ? '🌱' : '♻️' }} {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                        </span>
                    </td>
                    <td>
                        @php $conf = $item->confidence * 100; @endphp
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="confidence-bar-wrap">
                                <div class="confidence-bar {{ $conf >= 80 ? 'high' : ($conf >= 50 ? 'med' : 'low') }}"
                                     style="width:{{ number_format($conf, 0) }}%"></div>
                            </div>
                            <span class="text-sm font-bold" style="min-width:42px;text-align:right">{{ number_format($conf, 1) }}%</span>
                        </div>
                    </td>
                    <td class="text-sm muted">{{ $item->engine }}</td>
                    <td class="text-sm muted">{{ number_format($item->latency_ms) }} ms</td>
                    <td class="text-sm muted">{{ optional($item->detected_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-2)">
                        <div style="font-size:36px;margin-bottom:8px">🔬</div>
                        Belum ada data klasifikasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination" style="padding:16px 20px">{{ $classifications->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
registerPollCallback(async function() {
    try {
        const res = await fetch('{{ route("admin.stats") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (!res.ok) return;
        const data = await res.json();
        const el = document.getElementById('cls-total');
        if (el) el.textContent = data.total_classifications;
    } catch(e) {}
});
</script>
@endpush
