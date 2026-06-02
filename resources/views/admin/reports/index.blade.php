@extends('admin.layouts.app')
@section('title', 'Laporan')
@section('heading', 'Laporan Lingkungan')
@section('subheading', 'Verifikasi dan pemantauan laporan sampah dari pengguna aplikasi.')

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

    .report-image {
        width: 64px; height: 64px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid var(--line);
        cursor: pointer;
        transition: transform .15s;
    }
    .report-image:hover { transform: scale(1.06); }
    .img-placeholder {
        width: 64px; height: 64px;
        border-radius: 10px;
        background: var(--bg);
        border: 2px dashed var(--line);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        color: var(--text-2);
    }

    .urgency-high { color: #f87171; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.18); }
    .urgency-med { color: #fbbf24; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.18); }
    .urgency-low { color: #34d399; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.18); }

    .inline-form { display: flex; align-items: center; gap: 8px; }
    .status-select {
        padding: 6px 10px;
        border-radius: 8px;
        border: 1.5px solid var(--line);
        font-size: 12px;
        font-weight: 600;
        background: var(--card);
        color: var(--text);
        cursor: pointer;
        outline: none;
        transition: border-color .15s;
    }
    .status-select:focus { border-color: var(--primary); }

    /* Lightbox */
    .lightbox {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.85);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .lightbox.open { display: flex; }
    .lightbox img { max-width: 90vw; max-height: 88vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
    .lightbox-close {
        position: absolute; top: 20px; right: 24px;
        font-size: 36px; color: #fff; cursor: pointer; line-height: 1;
        transition: transform .15s;
    }
    .lightbox-close:hover { transform: scale(1.15); }

    .save-btn {
        padding: 6px 12px;
        border-radius: 8px;
        border: 1.5px solid var(--primary);
        background: var(--primary);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s;
    }
    .save-btn:hover { background: var(--primary-dark); }
    .save-btn:disabled { opacity:.5; cursor:not-allowed; }
    .tbl-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>
@endpush

@section('content')

{{-- Filter Tabs --}}
<div class="toolbar" style="margin-bottom:4px">
    <div class="filter-tabs" id="filter-tabs">
        <button class="filter-tab active" data-status="">Semua</button>
        <button class="filter-tab" data-status="Menunggu verifikasi">⏳ Menunggu</button>
        <button class="filter-tab" data-status="Diproses">🔄 Diproses</button>
        <button class="filter-tab" data-status="Selesai">✅ Selesai</button>
    </div>
    <div class="text-sm muted" id="report-count">{{ $reports->total() }} laporan</div>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <div class="tbl-scroll">
    <table id="reports-table" style="min-width:900px">
        <thead>
            <tr>
                <th style="width:80px;padding-left:20px">Foto</th>
                <th style="min-width:180px">Judul &amp; Pengguna</th>
                <th style="min-width:130px">Lokasi</th>
                <th style="min-width:130px">Kategori</th>
                <th>Urgensi</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="reports-tbody">
            @forelse($reports as $report)
                <tr data-id="{{ $report->id }}" data-status="{{ $report->status }}" class="report-row">
                    <td style="padding-left:20px">
                        @if($report->image_path)
                            <img
                                src="{{ url('report-images/' . basename($report->image_path)) }}"
                                class="report-image"
                                alt="Foto laporan"
                                onclick="openLightbox(this.src)"
                            >
                        @else
                            <div class="img-placeholder">📷</div>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold" style="max-width:200px">{{ $report->title }}</div>
                        <div class="text-sm muted">{{ $report->user?->display_name ?? '-' }}</div>
                    </td>
                    <td class="muted text-sm" style="max-width:160px">{{ $report->location_name }}</td>
                    <td><span class="badge badge-neutral text-sm">{{ $report->category }}</span></td>
                    <td>
                        <span class="badge {{ $report->urgency === 'Tinggi' ? 'urgency-high' : ($report->urgency === 'Sedang' ? 'urgency-med' : 'urgency-low') }}">
                            {{ $report->urgency === 'Tinggi' ? '🔴' : ($report->urgency === 'Sedang' ? '🟡' : '🟢') }} {{ $report->urgency }}
                        </span>
                    </td>
                    <td class="text-sm muted">{{ optional($report->reported_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $report->status === 'Selesai' ? 'badge-success' : ($report->status === 'Diproses' ? 'badge-warning' : 'badge-neutral') }}" id="badge-{{ $report->id }}">
                            {{ $report->status }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:6px; min-width:180px; padding-right:12px">
                            <div class="inline-form" style="display:flex; gap:6px">
                                <select class="status-select" id="sel-{{ $report->id }}" data-id="{{ $report->id }}" style="flex:1; padding: 5px 8px;">
                                    <option value="Menunggu verifikasi" @selected($report->status === 'Menunggu verifikasi')>Menunggu</option>
                                    <option value="Diproses" @selected($report->status === 'Diproses')>Diproses</option>
                                    <option value="Selesai" @selected($report->status === 'Selesai')>Selesai</option>
                                </select>
                                <button class="save-btn" onclick="updateStatus({{ $report->id }})" style="padding: 5px 10px;">Simpan</button>
                            </div>
                            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')" style="margin:0; width:100%">
                                @csrf @method('DELETE')
                                <button type="submit" class="save-btn" style="background:#dc2626; border-color:#dc2626; width:100%; display:block; text-align:center; padding:5px 0">🗑️ Hapus Laporan</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="empty-row">
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-2)">
                        <div style="font-size:36px;margin-bottom:8px">📋</div>
                        Belum ada laporan masuk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- /.tbl-scroll --}}

    <div class="pagination" style="padding:16px 20px">
        {{ $reports->links() }}
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <span class="lightbox-close" onclick="document.getElementById('lightbox').classList.remove('open')">×</span>
    <img id="lightbox-img" src="" alt="Foto laporan">
</div>

@endsection

@push('scripts')
<script>
// ── Lightbox ──
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox(e) {
    if (e.target === document.getElementById('lightbox')) {
        document.getElementById('lightbox').classList.remove('open');
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.getElementById('lightbox').classList.remove('open'); });

// ── Filter Tabs ──
let activeStatus = '';
document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeStatus = this.dataset.status;
        filterRows();
    });
});

// Check URL Search Params on load to filter automatically
const urlParams = new URLSearchParams(window.location.search);
const initialStatus = urlParams.get('status');
if (initialStatus) {
    const matchingBtn = document.querySelector(`.filter-tab[data-status="${initialStatus}"]`);
    if (matchingBtn) {
        matchingBtn.click();
    }
}

function filterRows() {
    let visible = 0;
    document.querySelectorAll('.report-row').forEach(row => {
        const match = !activeStatus || row.dataset.status === activeStatus;
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
}

// ── AJAX Status Update ──
async function updateStatus(id) {
    const sel = document.getElementById(`sel-${id}`);
    const btn = sel.nextElementSibling;
    const newStatus = sel.value;

    btn.disabled = true;
    btn.textContent = '...';

    try {
        const res = await fetch(`/admin/reports/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status: newStatus }),
        });

        if (!res.ok) throw new Error('Gagal');

        // Update badge
        const badge = document.getElementById(`badge-${id}`);
        if (badge) {
            badge.className = 'badge ' + (newStatus === 'Selesai' ? 'badge-success' : newStatus === 'Diproses' ? 'badge-warning' : 'badge-neutral');
            badge.textContent = newStatus;
        }
        // Update row data-status
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) row.dataset.status = newStatus;

        btn.textContent = '✓';
        btn.style.background = '#16a34a';
        setTimeout(() => { btn.textContent = 'Simpan'; btn.style.background = ''; btn.disabled = false; }, 1500);

        filterRows();
    } catch(e) {
        btn.textContent = 'Error';
        btn.style.background = '#dc2626';
        setTimeout(() => { btn.textContent = 'Simpan'; btn.style.background = ''; btn.disabled = false; }, 2000);
    }
}

// ── Realtime Polling: cek laporan baru ──
let knownIds = new Set([...document.querySelectorAll('.report-row')].map(r => r.dataset.id));

registerPollCallback(async function() {
    try {
        const res = await fetch('{{ route("admin.stats") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (!res.ok) return;
        const data = await res.json();
        document.getElementById('report-count').textContent = data.total_reports + ' laporan';
    } catch(e) {}
});
</script>
@endpush
