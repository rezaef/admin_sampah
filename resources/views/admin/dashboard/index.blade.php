@extends('admin.layouts.app')
@section('title', 'Dashboard Admin')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan aktivitas pengguna, klasifikasi, laporan, dan reward.')

@push('styles')
<style>
    .chart-wrap {
        position: relative;
        flex: 1;
        min-height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 12px 0;
    }
    .donut-center {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
    }
    .donut-center .val { font-size: 26px; font-weight: 900; color: var(--text); }
    .donut-center .lbl { font-size: 11px; color: var(--text-2); font-weight: 600; }
    .legend { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; justify-content: center; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-2); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
    .activity-row { display: flex; align-items: center; gap: 12px; }
    .activity-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px; color: #fff; flex-shrink: 0;
    }
    .report-thumb {
        width: 44px; height: 44px; border-radius: 8px;
        object-fit: cover; flex-shrink: 0;
        border: 2px solid var(--line-strong);
        cursor: pointer;
        transition: transform 0.15s ease, border-color 0.15s ease;
    }
    .report-thumb:hover {
        transform: scale(1.08);
        border-color: var(--primary);
    }
    .report-thumb-placeholder {
        width: 44px; height: 44px; border-radius: 8px;
        background: rgba(148,163,184,0.08); display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .confidence-bar-wrap { background: rgba(148,163,184,0.1); border-radius: 999px; height: 6px; flex: 1; min-width: 60px; }
    .confidence-bar { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #16a34a, #4ade80); }


    /* Stats Grouping Layout */
    .stats-group-row-1 {
        display: grid;
        gap: 20px;
        grid-template-columns: 3fr 2fr;
        margin-bottom: 20px;
    }
    .stats-group {
        background: rgba(148, 163, 184, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .stats-group-header {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 12px;
    }
    .stats-group-icon { font-size: 24px; }
    .stats-group-title { font-size: 16px; font-weight: 800; color: var(--text); }
    .stats-group-sub { font-size: 12px; color: var(--text-3); margin-top: 2px; }

    /* Group Card Items - Borderless and Flat nested inside */
    .stats-group .grid-3, .stats-group .grid-2 {
        display: grid;
        gap: 16px;
    }
    .stats-group .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .stats-group .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    .stat-tile {
        background: rgba(148, 163, 184, 0.04);
        border-radius: var(--radius);
        padding: 18px 34px 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: background 0.25s, transform 0.25s, box-shadow 0.25s;
        position: relative;
        text-decoration: none;
        color: inherit;
    }
    .stat-tile:hover {
        background: rgba(148, 163, 184, 0.08);
        transform: translateY(-3px);
    }
    .stat-tile.green:hover { box-shadow: 0 12px 24px -10px rgba(34, 197, 94, 0.2); }
    .stat-tile.blue:hover { box-shadow: 0 12px 24px -10px rgba(14, 165, 233, 0.2); }
    .stat-tile.orange:hover { box-shadow: 0 12px 24px -10px rgba(249, 115, 22, 0.2); }
    .stat-tile.purple:hover { box-shadow: 0 12px 24px -10px rgba(139, 92, 246, 0.2); }
    .stat-tile.teal:hover { box-shadow: 0 12px 24px -10px rgba(13, 148, 136, 0.2); }
    .stat-tile.indigo:hover { box-shadow: 0 12px 24px -10px rgba(79, 70, 229, 0.2); }
    .stat-tile.red:hover { box-shadow: 0 12px 24px -10px rgba(239, 68, 68, 0.2); }
    .stat-tile.yellow:hover { box-shadow: 0 12px 24px -10px rgba(202, 138, 4, 0.2); }
    
    /* Indicator strip on left */
    .stat-tile::before {
        content: '';
        position: absolute;
        left: 0; top: 16px; bottom: 16px;
        width: 5px;
        border-radius: 0 99px 99px 0;
    }
    .stat-tile.green::before { background: #16a34a; }
    .stat-tile.blue::before { background: #0ea5e9; }
    .stat-tile.orange::before { background: #f97316; }
    .stat-tile.purple::before { background: #8b5cf6; }
    .stat-tile.teal::before { background: #0d9488; }
    .stat-tile.indigo::before { background: #4f46e5; }
    .stat-tile.red::before { background: #dc2626; }
    .stat-tile.yellow::before { background: #ca8a04; }

    .stat-tile-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        transition: transform 0.2s;
    }
    .stat-tile:hover .stat-tile-icon { transform: scale(1.1) rotate(-3deg); }
    .stat-tile-icon.green { background: rgba(34,197,94,0.1); }
    .stat-tile-icon.blue { background: rgba(14,165,233,0.1); }
    .stat-tile-icon.orange { background: rgba(249,115,22,0.1); }
    .stat-tile-icon.purple { background: rgba(139,92,246,0.1); }
    .stat-tile-icon.red { background: rgba(239,68,68,0.1); }
    .stat-tile-icon.teal { background: rgba(13,148,136,0.1); }
    .stat-tile-icon.yellow { background: rgba(202,138,4,0.1); }
    .stat-tile-icon.indigo { background: rgba(79,70,229,0.1); }

    .stat-tile-content {
        flex: 1;
        min-width: 0;
    }
    .stat-tile-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-3);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .stat-tile-value {
        font-size: 28px;
        font-weight: 900;
        color: var(--text);
        margin-top: 2px;
        line-height: 1.2;
    }

    .stat-tile::after {
        content: '→';
        font-size: 16px;
        color: var(--text-3);
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%) translateX(-6px);
    }
    .stat-tile:hover::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
        color: var(--primary);
    }

    /* Responsive adjustments for laptop screens */
    @media (max-width: 1440px) {
        .stats-group {
            padding: 16px;
            gap: 12px;
        }
        .stats-group-header {
            padding-bottom: 8px;
        }
        .stat-tile {
            padding: 12px 26px 12px 14px;
            gap: 10px;
            border-radius: var(--radius-sm);
        }
        .stat-tile::before {
            top: 12px; bottom: 12px;
            width: 4px;
        }
        .stat-tile-icon {
            width: 38px; height: 38px;
            font-size: 18px;
            border-radius: 8px;
        }
        .stat-tile-value {
            font-size: 25px;
        }
        .stat-tile-label {
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .stats-group .grid-3, .stats-group .grid-2 {
            gap: 10px;
        }
    }

    @media (max-width: 1300px) {
        .stats-group-row-1 {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 768px) {
        .stats-group .grid-3, .stats-group .grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* Floating Tooltip */
    .chart-tooltip {
        position: fixed;
        pointer-events: none;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 12px;
        z-index: 9999;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: opacity 0.15s cubic-bezier(0.23,1,0.32,1), transform 0.15s cubic-bezier(0.23,1,0.32,1);
        opacity: 0;
        transform: scale(0.95);
        visibility: hidden;
    }
    .chart-tooltip.show {
        opacity: 1;
        transform: scale(1);
        visibility: visible;
    }

    /* Urgency Badge Styles */
    .urgency-high { color: #f87171; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.18); }
    .urgency-med { color: #fbbf24; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.18); }
    .urgency-low { color: #34d399; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.18); }
</style>
@endpush

@section('content')

{{-- ── Stat Cards Grouped ── --}}
<div class="stats-group-row-1">
    {{-- Group 1: Analisis Klasifikasi --}}
    <div class="stats-group">
        <div class="stats-group-header">
            <span class="stats-group-icon">🔬</span>
            <div>
                <div class="stats-group-title">Analisis Klasifikasi Sampah</div>
                <div class="stats-group-sub">Statistik pemindaian dan deteksi sampah oleh AI</div>
            </div>
        </div>
        <div class="grid-3">
            <a href="{{ route('admin.classifications.index') }}" class="stat-tile blue">
                <div class="stat-tile-icon blue">🔬</div>
                <div class="stat-tile-content">
                    <div class="stat-tile-label">Total Klasifikasi</div>
                    <div class="stat-tile-value" id="stat-cls">{{ $stats['total_classifications'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.classifications.index', ['category' => 'organik']) }}" class="stat-tile teal">
                <div class="stat-tile-icon teal">🌱</div>
                <div class="stat-tile-content">
                    <div class="stat-tile-label">Sampah Organik</div>
                    <div class="stat-tile-value" id="stat-organic">{{ $stats['organic_count'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.classifications.index', ['category' => 'anorganik']) }}" class="stat-tile indigo">
                <div class="stat-tile-icon indigo">♻️</div>
                <div class="stat-tile-content">
                    <div class="stat-tile-label">Sampah Anorganik</div>
                    <div class="stat-tile-value" id="stat-anorganic">{{ $stats['anorganic_count'] }}</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Group 2: Pengguna & Akses --}}
    <div class="stats-group">
        <div class="stats-group-header">
            <span class="stats-group-icon">👥</span>
            <div>
                <div class="stats-group-title">Kependudukan & Akses</div>
                <div class="stats-group-sub">Manajemen status akun dan hak akses pengguna</div>
            </div>
        </div>
        <div class="grid-2">
            <a href="{{ route('admin.users.index', ['role' => 'user']) }}" class="stat-tile green">
                <div class="stat-tile-icon green">👥</div>
                <div class="stat-tile-content">
                    <div class="stat-tile-label">Total Pengguna</div>
                    <div class="stat-tile-value" id="stat-users">{{ $stats['total_users'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="stat-tile yellow">
                <div class="stat-tile-icon yellow">🛡️</div>
                <div class="stat-tile-content">
                    <div class="stat-tile-label">Total Admin</div>
                    <div class="stat-tile-value" id="stat-admins">{{ $stats['total_admins'] }}</div>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Group 3: Laporan & Penukaran --}}
<div class="stats-group" style="margin-bottom: 20px;">
    <div class="stats-group-header">
        <span class="stats-group-icon">🔔</span>
        <div>
            <div class="stats-group-title">Aktivitas Laporan & Penukaran Reward</div>
            <div class="stats-group-sub">Validasi laporan lingkungan dan proses penukaran reward</div>
        </div>
    </div>
    <div class="grid-3">
        <a href="{{ route('admin.reports.index') }}" class="stat-tile orange">
            <div class="stat-tile-icon orange">📋</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Total Laporan</div>
                <div class="stat-tile-value" id="stat-reports">{{ $stats['total_reports'] }}</div>
            </div>
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'Menunggu verifikasi']) }}" class="stat-tile red">
            <div class="stat-tile-icon red">⏳</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Menunggu Verifikasi</div>
                <div class="stat-tile-value" id="stat-pending">{{ $stats['pending_reports'] }}</div>
            </div>
        </a>
        <a href="{{ route('admin.redemptions.index') }}" class="stat-tile purple">
            <div class="stat-tile-icon purple">🎁</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Penukaran Reward</div>
                <div class="stat-tile-value" id="stat-redeem">{{ $stats['total_redemptions'] }}</div>
            </div>
        </a>
    </div>
</div>

{{-- Group 4: Urgensi Laporan --}}
<div class="stats-group" style="margin-bottom: 20px;">
    <div class="stats-group-header">
        <span class="stats-group-icon">⚠️</span>
        <div>
            <div class="stats-group-title">Kadar Urgensi Laporan Lingkungan</div>
            <div class="stats-group-sub">Pengelompokan laporan berdasarkan tingkat kedaruratan</div>
        </div>
    </div>
    <div class="grid-3">
        <a href="{{ route('admin.reports.index') }}" class="stat-tile red" style="box-shadow: 0 4px 14px rgba(239, 68, 68, 0.05);">
            <div class="stat-tile-icon red">🔴</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Urgensi Tinggi</div>
                <div class="stat-tile-value" id="stat-urgency-high">{{ $stats['high_urgency_count'] }}</div>
            </div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="stat-tile orange" style="box-shadow: 0 4px 14px rgba(249, 115, 22, 0.05);">
            <div class="stat-tile-icon orange">🟡</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Urgensi Sedang</div>
                <div class="stat-tile-value" id="stat-urgency-medium">{{ $stats['medium_urgency_count'] }}</div>
            </div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="stat-tile green" style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.05);">
            <div class="stat-tile-icon green">🟢</div>
            <div class="stat-tile-content">
                <div class="stat-tile-label">Urgensi Rendah</div>
                <div class="stat-tile-value" id="stat-urgency-low">{{ $stats['low_urgency_count'] }}</div>
            </div>
        </a>
    </div>
</div>

{{-- ── Chart + Recent Reports ── --}}
<div class="grid-2">
    {{-- Donut Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Distribusi Klasifikasi</div>
                <div class="card-sub">Organik vs Anorganik dari total scan</div>
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="donutChart" width="200" height="200" style="display: block; width: 200px; height: 200px;"
                    data-organic-avg="{{ number_format($stats['organic_avg_confidence'] * 100, 1) }}"
                    data-anorganic-avg="{{ number_format($stats['anorganic_avg_confidence'] * 100, 1) }}"
                    data-other-avg="{{ number_format($stats['other_avg_confidence'] * 100, 1) }}"></canvas>
            <div class="donut-center">
                <div class="val" id="donut-center-val" data-total="{{ $stats['total_classifications'] }}">{{ $stats['total_classifications'] }}</div>
                <div class="lbl" id="donut-center-lbl">Total Scan</div>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item" style="display:flex; flex-direction:column; align-items:center;">
                <div style="display:flex; align-items:center; gap:6px;"><span class="legend-dot" style="background:#16a34a"></span>Organik ({{ $stats['organic_count'] }})</div>
                <span class="text-sm muted" id="legend-organic-avg" style="font-size:10px; margin-top:2px;">Rata-rata Kepercayaan: {{ number_format($stats['organic_avg_confidence'] * 100, 1) }}%</span>
            </div>
            <div class="legend-item" style="display:flex; flex-direction:column; align-items:center;">
                <div style="display:flex; align-items:center; gap:6px;"><span class="legend-dot" style="background:#0ea5e9"></span>Anorganik ({{ $stats['anorganic_count'] }})</div>
                <span class="text-sm muted" id="legend-anorganic-avg" style="font-size:10px; margin-top:2px;">Rata-rata Kepercayaan: {{ number_format($stats['anorganic_avg_confidence'] * 100, 1) }}%</span>
            </div>
            @php $other = $stats['total_classifications'] - $stats['organic_count'] - $stats['anorganic_count']; @endphp
            @if($other > 0)
                <div class="legend-item" style="display:flex; flex-direction:column; align-items:center;">
                    <div style="display:flex; align-items:center; gap:6px;"><span class="legend-dot" style="background:#475569"></span>Lainnya ({{ $other }})</div>
                    <span class="text-sm muted" id="legend-other-avg" style="font-size:10px; margin-top:2px;">Rata-rata Kepercayaan: {{ number_format($stats['other_avg_confidence'] * 100, 1) }}%</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Laporan Terbaru</div>
                <div class="card-sub">Status dan lokasi laporan lingkungan terbaru</div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm">Lihat semua</a>
        </div>
        <div class="tbl-scroll">
            <table>
                <thead>
                    <tr>
                        <th style="width:52px"></th>
                        <th>Judul</th>
                        <th>Lokasi</th>
                        <th>Urgensi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recent-reports-tbody">
                    @forelse($recentReports as $report)
                        <tr data-id="{{ $report->id }}">
                            <td>
                                @if($report->image_path)
                                    <img src="{{ $report->image_url }}" class="report-thumb" alt="" onclick="openLightbox(this.src)" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="report-thumb-placeholder" style="display:none">📷</div>
                                @else
                                    <div class="report-thumb-placeholder">📷</div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold truncate" style="max-width:140px" title="{{ $report->title }}">{{ $report->title }}</div>
                                <div class="text-sm muted">{{ optional($report->reported_at)->format('d/m H:i') }}</div>
                            </td>
                            <td>
                                <div class="truncate" style="max-width:120px" title="{{ $report->location_name }}">{{ $report->location_name }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $report->urgency === 'Tinggi' ? 'urgency-high' : ($report->urgency === 'Sedang' ? 'urgency-med' : 'urgency-low') }}" style="padding: 2px 8px; font-size: 10px;">
                                    {{ $report->urgency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $report->status === 'Selesai' ? 'badge-success' : ($report->status === 'Diproses' ? 'badge-warning' : 'badge-neutral') }}">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted" style="text-align:center;padding:24px">Belum ada laporan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recent Classifications --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Klasifikasi Terbaru</div>
            <div class="card-sub">Riwayat scan terbaru dari pengguna mobile</div>
        </div>
        <a href="{{ route('admin.classifications.index') }}" class="btn btn-sm">Lihat semua</a>
    </div>
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Kategori</th>
                    <th>Confidence</th>
                    <th>Engine</th>
                    <th>Latency</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody id="recent-cls-tbody">
                @forelse($recentClassifications as $item)
                    <tr>
                        <td>
                            <div class="activity-row">
                                @php $colors = ['#16a34a','#0ea5e9','#8b5cf6','#f97316','#dc2626']; $c = $colors[crc32($item->user?->display_name ?? 'X') % count($colors)]; @endphp
                                <div class="activity-avatar" style="background:{{ $c }}">{{ strtoupper(substr($item->user?->display_name ?? '?', 0, 1)) }}</div>
                                <span class="font-bold">{{ $item->user?->display_name ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ str_contains($item->category,'organik') && !str_contains($item->category,'an') ? 'badge-success' : 'badge-blue' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                            </span>
                        </td>
                        <td style="min-width:120px">
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="confidence-bar-wrap">
                                    <div class="confidence-bar" style="width:{{ number_format($item->confidence * 100, 0) }}%"></div>
                                </div>
                                <span class="text-sm font-bold">{{ number_format($item->confidence * 100, 1) }}%</span>
                            </div>
                        </td>
                        <td class="text-sm muted">{{ $item->engine }}</td>
                        <td class="text-sm muted">{{ $item->latency_ms }} ms</td>
                        <td class="text-sm muted">{{ optional($item->detected_at)->format('d/m H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted" style="text-align:center;padding:24px">Belum ada data klasifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>

// ── Donut Chart ──
(function() {
    const canvas = document.getElementById('donutChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const organic = {{ $stats['organic_count'] }};
    const anorganic = {{ $stats['anorganic_count'] }};
    const other = Math.max(0, {{ $stats['total_classifications'] }} - organic - anorganic);

    // Dynamic segment states to track progress and target scaling
    const segments = [
        { label: 'Organik', value: organic, color: '#16a34a', progress: 0, target: 0 },
        { label: 'Anorganik', value: anorganic, color: '#0ea5e9', progress: 0, target: 0 },
        { label: 'Lainnya', value: other, color: '#475569', progress: 0, target: 0 },
    ];

    function drawDonut() {
        const activeSegments = segments.filter(s => s.value > 0);
        const totalVal = activeSegments.reduce((sum, s) => sum + s.value, 0) || 1;
        const cx = 100, cy = 100, r = 52;
        ctx.clearRect(0, 0, 200, 200);

        let start = -Math.PI / 2;
        activeSegments.forEach(seg => {
            const sweep = (seg.value / totalVal) * 2 * Math.PI;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            
            // Interpolate radius between 80 (normal) and 86 (hovered) based on segment animation progress
            const currentR = 80 + (6 * seg.progress);
            ctx.arc(cx, cy, currentR, start, start + sweep);
            
            ctx.closePath();
            ctx.fillStyle = seg.color;
            ctx.fill();
            start += sweep;
        });

        // cutout
        ctx.beginPath();
        ctx.arc(cx, cy, r, 0, 2 * Math.PI);
        const cardSolidColor = getComputedStyle(document.documentElement).getPropertyValue('--card-solid').trim() || '#1e293b';
        ctx.fillStyle = cardSolidColor;
        ctx.fill();
    }

    let animationFrameId = null;

    function animate() {
        let changed = false;
        segments.forEach(seg => {
            const diff = seg.target - seg.progress;
            if (Math.abs(diff) > 0.001) {
                seg.progress += diff * 0.15; // Smooth interpolation speed
                changed = true;
            } else {
                seg.progress = seg.target;
            }
        });

        drawDonut();

        if (changed) {
            animationFrameId = requestAnimationFrame(animate);
        } else {
            animationFrameId = null;
        }
    }

    function triggerAnimation() {
        if (!animationFrameId) {
            animationFrameId = requestAnimationFrame(animate);
        }
    }

    // Initial Draw
    drawDonut();

    // Create tooltip dynamically if not exists
    let tooltip = document.getElementById('chart-tooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = 'chart-tooltip';
        tooltip.className = 'chart-tooltip';
        document.body.appendChild(tooltip);
    }

    // Interactive Hover Tracking
    let lastHoveredIndex = -1;
    canvas.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        const scaleX = 200 / rect.width;
        const scaleY = 200 / rect.height;
        const x = (e.clientX - rect.left) * scaleX;
        const y = (e.clientY - rect.top) * scaleY;
        
        const dx = x - 100;
        const dy = y - 100;
        const dist = Math.sqrt(dx * dx + dy * dy);
        
        let hoveredIndex = -1;
        const currentOrganic = parseInt(document.getElementById('stat-organic')?.textContent) || organic;
        const currentAnorganic = parseInt(document.getElementById('stat-anorganic')?.textContent) || anorganic;
        const valEl = document.getElementById('donut-center-val');
        const currentTotal = parseInt(valEl?.dataset.total || (currentOrganic + currentAnorganic + other));
        const currentOther = Math.max(0, currentTotal - currentOrganic - currentAnorganic);
        const totalVal = currentOrganic + currentAnorganic + currentOther || 1;
        
        // Update segment values dynamically for current active calculations
        const orgSeg = segments.find(s => s.label === 'Organik');
        if (orgSeg) orgSeg.value = currentOrganic;
        const anSeg = segments.find(s => s.label === 'Anorganik');
        if (anSeg) anSeg.value = currentAnorganic;
        const othSeg = segments.find(s => s.label === 'Lainnya');
        if (othSeg) othSeg.value = currentOther;

        // Expanded max distance is 86, so hit test works up to 88
        if (dist >= 52 && dist <= 88) {
            let angle = Math.atan2(dy, dx);
            if (angle < -Math.PI / 2) {
                angle += 2 * Math.PI;
            }
            
            const activeSegments = segments.filter(s => s.value > 0);
            
            let start = -Math.PI / 2;
            for (let i = 0; i < activeSegments.length; i++) {
                const sweep = (activeSegments[i].value / totalVal) * 2 * Math.PI;
                if (angle >= start && angle < start + sweep) {
                    hoveredIndex = i;
                    break;
                }
                start += sweep;
            }
        }
        
        if (hoveredIndex !== lastHoveredIndex) {
            lastHoveredIndex = hoveredIndex;
            
            // Set animation targets (1 for hovered segment, 0 for others)
            const activeSegments = segments.filter(s => s.value > 0);
            activeSegments.forEach((seg, idx) => {
                seg.target = (idx === hoveredIndex) ? 1 : 0;
            });
            triggerAnimation();
        }

        // Display and position tooltip following cursor
        if (hoveredIndex !== -1) {
            const activeSegments = segments.filter(s => s.value > 0);
            const seg = activeSegments[hoveredIndex];
            const percent = ((seg.value / totalVal) * 100).toFixed(0);
            
            const organicAvg = canvas.dataset.organicAvg || '0.0';
            const anorganicAvg = canvas.dataset.anorganicAvg || '0.0';
            const otherAvg = canvas.dataset.otherAvg || '0.0';
            let avgConf = '0.0%';
            if (seg.label === 'Organik') avgConf = organicAvg + '%';
            else if (seg.label === 'Anorganik') avgConf = anorganicAvg + '%';
            else if (seg.label === 'Lainnya') avgConf = otherAvg + '%';

            tooltip.innerHTML = `
                <div style="font-weight: 800; font-size: 12px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; color:#fff;">
                    <span style="width:8px; height:8px; border-radius:50%; background:${seg.color}; display:inline-block;"></span>
                    <span>${seg.label} (${percent}%)</span>
                </div>
                <div style="color: rgba(255,255,255,0.75); font-size: 10px; font-weight: 500;">
                    Rata-rata Kepercayaan: <span style="color:#fff; font-weight: 700;">${avgConf}</span>
                </div>
            `;
            // Position near cursor with offset
            tooltip.style.left = (e.clientX + 14) + 'px';
            tooltip.style.top = (e.clientY + 14) + 'px';
            tooltip.classList.add('show');
        } else {
            tooltip.classList.remove('show');
        }

        canvas.style.cursor = hoveredIndex !== -1 ? 'pointer' : 'default';
    });

    canvas.addEventListener('mouseleave', () => {
        tooltip.classList.remove('show');
        if (lastHoveredIndex !== -1) {
            lastHoveredIndex = -1;
            
            // Reset all segment targets to 0 for smooth scale-in animation
            segments.forEach(seg => {
                seg.target = 0;
            });
            triggerAnimation();
        }
    });

    // Expose for polling update
    window._updateDonut = function(o, an, tot, oAvg = 0, anAvg = 0, othAvg = 0) {
        const oth = Math.max(0, tot - o - an);
        
        // Update current segment values
        const orgSeg = segments.find(s => s.label === 'Organik');
        if (orgSeg) orgSeg.value = o;
        const anSeg = segments.find(s => s.label === 'Anorganik');
        if (anSeg) anSeg.value = an;
        const othSeg = segments.find(s => s.label === 'Lainnya');
        if (othSeg) othSeg.value = oth;
        
        triggerAnimation();
        
        // Update dataset
        canvas.dataset.organicAvg = parseFloat(oAvg * 100).toFixed(1);
        canvas.dataset.anorganicAvg = parseFloat(anAvg * 100).toFixed(1);
        canvas.dataset.otherAvg = parseFloat(othAvg * 100).toFixed(1);

        // Update legends
        const legOrganic = document.getElementById('legend-organic-avg');
        if (legOrganic) legOrganic.textContent = `Rata-rata Kepercayaan: ${parseFloat(oAvg * 100).toFixed(1)}%`;
        const legAnorganic = document.getElementById('legend-anorganic-avg');
        if (legAnorganic) legAnorganic.textContent = `Rata-rata Kepercayaan: ${parseFloat(anAvg * 100).toFixed(1)}%`;
        const legOther = document.getElementById('legend-other-avg');
        if (legOther) legOther.textContent = `Rata-rata Kepercayaan: ${parseFloat(othAvg * 100).toFixed(1)}%`;

        const valEl = document.getElementById('donut-center-val');
        if (valEl) {
            valEl.dataset.total = tot;
            valEl.textContent = tot; // Always keep total scan in center
        }
    };

    // Redraw chart when theme changes
    window.addEventListener('theme-changed', () => {
        drawDonut();
    });
})();

// ── Realtime Polling ──
// Use sessionStorage to persist the highest known report ID across page navigations.
// This prevents false notifications when switching between admin pages.
(function() {
    const STORAGE_KEY = 'dashboard_highest_report_id';

    // Get highest ID from initial server-rendered table rows
    const initialIds = [...document.querySelectorAll('#recent-reports-tbody tr[data-id]')]
        .map(tr => parseInt(tr.dataset.id) || 0);
    const initialMaxId = initialIds.length > 0 ? Math.max(...initialIds) : 0;

    // Restore from sessionStorage or use initial DOM value
    const storedId = parseInt(sessionStorage.getItem(STORAGE_KEY)) || 0;
    let highestKnownId = Math.max(storedId, initialMaxId);

    // Save so future page loads don't re-notify
    sessionStorage.setItem(STORAGE_KEY, highestKnownId);

    registerPollCallback(async function() {
        try {
            const res = await fetch('{{ route("admin.stats") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            if (!res.ok) return;
            const data = await res.json();

            // Update statistics counters with animation
            const map = {
                'stat-users': data.total_users,
                'stat-cls': data.total_classifications,
                'stat-reports': data.total_reports,
                'stat-redeem': data.total_redemptions,
                'stat-organic': data.organic_count,
                'stat-anorganic': data.anorganic_count,
                'stat-pending': data.pending_reports,
                'stat-admins': data.total_admins,
                'stat-urgency-high': data.high_urgency_count,
                'stat-urgency-medium': data.medium_urgency_count,
                'stat-urgency-low': data.low_urgency_count,
            };
            for (const [id, val] of Object.entries(map)) {
                const el = document.getElementById(id);
                if (el && parseInt(el.textContent) !== val) animateCounter(el, val);
            }
            if (window._updateDonut) {
                window._updateDonut(
                    data.organic_count, 
                    data.anorganic_count, 
                    data.total_classifications,
                    data.organic_avg_confidence,
                    data.anorganic_avg_confidence,
                    data.other_avg_confidence
                );
            }

            // Process new reports — only notify for genuinely new ones
            if (data.recent_reports && data.recent_reports.length > 0) {
                // Find reports that are genuinely new (ID > our tracked maximum)
                const newReports = data.recent_reports
                    .filter(r => r.id > highestKnownId)
                    .sort((a, b) => a.id - b.id); // process oldest first

                // Fire notifications ONLY for genuinely new reports
                newReports.forEach(report => {
                    // 1. Show HTML Toast Notification
                    if (window.showToastNotification) {
                        window.showToastNotification(
                            `Laporan Baru: ${report.title}`,
                            `Dari: ${report.user_name} | Lokasi: ${report.location_name}`,
                            report.urgency,
                            '{{ route("admin.reports.index") }}'
                        );
                    }

                    // 2. Play Audio beep
                    if (window.playNotificationSound) {
                        window.playNotificationSound();
                    }

                    // 3. Browser Push Notification
                    if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                        new Notification(`Sampah Detector: Laporan Baru!`, {
                            body: `${report.title} (${report.urgency}) di ${report.location_name}\nOleh: ${report.user_name}`,
                            icon: '♻️'
                        });
                    }

                    // 4. Prepend new row to "Laporan Terbaru" table
                    const tbody = document.getElementById('recent-reports-tbody');
                    if (tbody) {
                        // Remove empty placeholder row if present
                        const placeholder = tbody.querySelector('td[colspan]');
                        if (placeholder) {
                            placeholder.closest('tr').remove();
                        }

                        // Don't add duplicate rows
                        if (!tbody.querySelector(`tr[data-id="${report.id}"]`)) {
                            const tr = document.createElement('tr');
                            tr.dataset.id = report.id;
                            tr.className = 'row-new';
                            
                            const imgHtml = report.image_url 
                                ? `<img src="${report.image_url}" class="report-thumb" alt="" onclick="openLightbox(this.src)" onerror="this.parentElement.innerHTML='<div class=\\'report-thumb-placeholder\\'>📷</div>'">`
                                : `<div class="report-thumb-placeholder">📷</div>`;
                                
                            const urgencyBadgeClass = report.urgency === 'Tinggi' ? 'urgency-high' : (report.urgency === 'Sedang' ? 'urgency-med' : 'urgency-low');
                            const statusBadgeClass = report.status === 'Selesai' ? 'badge-success' : (report.status === 'Diproses' ? 'badge-warning' : 'badge-neutral');

                            tr.innerHTML = `
                                <td>${imgHtml}</td>
                                <td>
                                    <div class="font-bold truncate" style="max-width:140px" title="${report.title}">${report.title}</div>
                                    <div class="text-sm muted">${report.time_formatted}</div>
                                </td>
                                <td>
                                    <div class="truncate" style="max-width:120px" title="${report.location_name}">${report.location_name}</div>
                                </td>
                                <td>
                                    <span class="badge ${urgencyBadgeClass}" style="padding: 2px 8px; font-size: 10px;">${report.urgency}</span>
                                </td>
                                <td>
                                    <span class="badge ${statusBadgeClass}">${report.status}</span>
                                </td>
                            `;
                            tbody.insertBefore(tr, tbody.firstChild);

                            // Ensure maximum of 5 rows
                            while (tbody.children.length > 5) {
                                tbody.lastChild.remove();
                            }
                        }
                    }
                });

                // Update highestKnownId to the maximum of all received reports
                if (newReports.length > 0) {
                    highestKnownId = Math.max(highestKnownId, ...newReports.map(r => r.id));
                    sessionStorage.setItem(STORAGE_KEY, highestKnownId);
                }
            }
        } catch(e) {
            console.warn('Realtime polling error:', e);
        }
    });
})();
</script>
@endpush
