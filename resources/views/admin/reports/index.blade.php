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
        position: fixed; top: 20px; right: 24px;
        font-size: 42px; color: #fff; cursor: pointer; line-height: 1;
        z-index: 10000;
        transition: transform .15s;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
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
<div class="toolbar" style="margin-bottom:12px">
    <div class="filter-tabs" id="filter-tabs">
        <button class="filter-tab active" data-status="">Semua</button>
        <button class="filter-tab" data-status="Menunggu verifikasi">⏳ Menunggu</button>
        <button class="filter-tab" data-status="Diproses">🔄 Diproses</button>
        <button class="filter-tab" data-status="Selesai">✅ Selesai</button>
    </div>
    <div class="text-sm muted" id="report-count">{{ $totalCount }} laporan</div>
</div>

{{-- Section 1: Urgensi Tinggi --}}
<div class="card urgency-card tinggi-card" style="margin-bottom: 24px; border-top: 4px solid var(--danger); padding:0; overflow:hidden">
    <div class="card-header" style="padding: 20px 20px 8px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div class="card-title" style="display:flex; align-items:center; gap:8px; font-size:16px;">
                <span>🔴</span> Urgensi Tinggi
            </div>
            <div class="card-sub">Laporan mendesak yang membutuhkan penanganan segera</div>
        </div>
        <span class="badge badge-danger" id="count-tinggi">{{ count($reportsTinggi) }} laporan</span>
    </div>
    <div class="tbl-scroll">
        <table style="min-width:900px">
            <thead>
                <tr>
                    <th style="width:80px;padding-left:20px">Foto</th>
                    <th style="min-width:180px">Judul &amp; Pengguna</th>
                    <th style="min-width:130px">Lokasi</th>
                    <th style="min-width:130px">Kategori</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="reports-tbody" id="tbody-tinggi">
                @forelse($reportsTinggi as $report)
                    @include('admin.reports._report_row', ['report' => $report])
                @empty
                    <tr class="empty-row-placeholder">
                        <td colspan="7" style="text-align:center;padding:32px;color:var(--text-2)">
                            <div style="font-size:28px;margin-bottom:6px">👍</div>
                            Bagus sekali! Tidak ada laporan dengan urgensi tinggi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Section 2: Urgensi Sedang --}}
<div class="card urgency-card sedang-card" style="margin-bottom: 24px; border-top: 4px solid var(--warning); padding:0; overflow:hidden">
    <div class="card-header" style="padding: 20px 20px 8px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div class="card-title" style="display:flex; align-items:center; gap:8px; font-size:16px;">
                <span>🟡</span> Urgensi Sedang
            </div>
            <div class="card-sub">Laporan tingkat menengah untuk dijadwalkan tindak lanjut</div>
        </div>
        <span class="badge badge-warning" id="count-sedang">{{ count($reportsSedang) }} laporan</span>
    </div>
    <div class="tbl-scroll">
        <table style="min-width:900px">
            <thead>
                <tr>
                    <th style="width:80px;padding-left:20px">Foto</th>
                    <th style="min-width:180px">Judul &amp; Pengguna</th>
                    <th style="min-width:130px">Lokasi</th>
                    <th style="min-width:130px">Kategori</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="reports-tbody" id="tbody-sedang">
                @forelse($reportsSedang as $report)
                    @include('admin.reports._report_row', ['report' => $report])
                @empty
                    <tr class="empty-row-placeholder">
                        <td colspan="7" style="text-align:center;padding:32px;color:var(--text-2)">
                            <div style="font-size:28px;margin-bottom:6px">👍</div>
                            Tidak ada laporan dengan urgensi sedang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Section 3: Urgensi Rendah --}}
<div class="card urgency-card rendah-card" style="margin-bottom: 24px; border-top: 4px solid var(--success); padding:0; overflow:hidden">
    <div class="card-header" style="padding: 20px 20px 8px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div class="card-title" style="display:flex; align-items:center; gap:8px; font-size:16px;">
                <span>🟢</span> Urgensi Rendah
            </div>
            <div class="card-sub">Laporan minor atau informasi tambahan kebersihan lingkungan</div>
        </div>
        <span class="badge badge-success" id="count-rendah">{{ count($reportsRendah) }} laporan</span>
    </div>
    <div class="tbl-scroll">
        <table style="min-width:900px">
            <thead>
                <tr>
                    <th style="width:80px;padding-left:20px">Foto</th>
                    <th style="min-width:180px">Judul &amp; Pengguna</th>
                    <th style="min-width:130px">Lokasi</th>
                    <th style="min-width:130px">Kategori</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="reports-tbody" id="tbody-rendah">
                @forelse($reportsRendah as $report)
                    @include('admin.reports._report_row', ['report' => $report])
                @empty
                    <tr class="empty-row-placeholder">
                        <td colspan="7" style="text-align:center;padding:32px;color:var(--text-2)">
                            <div style="font-size:28px;margin-bottom:6px">👍</div>
                            Tidak ada laporan dengan urgensi rendah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <span class="lightbox-close" onclick="closeLightbox(event)">×</span>
    <img id="lightbox-img" src="" alt="Foto laporan">
</div>

@endsection

@push('scripts')
<script>
// ── Lightbox ──
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
    if (e === undefined || e.target === document.getElementById('lightbox') || e.target.classList.contains('lightbox-close')) {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

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
    let totalVisible = 0;
    
    // Loop through each table body
    document.querySelectorAll('.reports-tbody').forEach(tbody => {
        const rows = tbody.querySelectorAll('.report-row');
        let visibleCount = 0;
        rows.forEach(row => {
            const match = !activeStatus || row.dataset.status === activeStatus;
            row.style.display = match ? '' : 'none';
            if (match) {
                visibleCount++;
                totalVisible++;
            }
        });

        // Hide normal empty placeholder row if it exists
        const normalPlaceholder = tbody.querySelector('.empty-row-placeholder');
        if (normalPlaceholder) {
            normalPlaceholder.style.display = activeStatus ? 'none' : '';
            if (!activeStatus) {
                // If status is empty and we had normal placeholder, visible count is 0
                visibleCount = 0;
            }
        }

        // Toggle local empty row for current filter
        let emptyRow = tbody.querySelector('.local-empty-row');
        if (visibleCount === 0 && (!normalPlaceholder || normalPlaceholder.style.display === 'none')) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.className = 'local-empty-row';
                emptyRow.innerHTML = `
                    <td colspan="7" style="text-align:center;padding:24px;color:var(--text-3);font-weight:600;">
                        Tidak ada laporan dengan status ini.
                    </td>
                `;
                tbody.appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
            }
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
        }
    });
    
    document.getElementById('report-count').textContent = totalVisible + ' laporan';
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
let highestKnownId = Math.max(...[...document.querySelectorAll('.report-row')].map(r => parseInt(r.dataset.id) || 0), 0);

registerPollCallback(async function() {
    try {
        const res = await fetch('{{ route("admin.stats") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (!res.ok) return;
        const data = await res.json();
        
        // Count total reports for visual label
        document.getElementById('report-count').textContent = data.total_reports + ' Laporan';
        
        // Notify page user if there is a new report
        if (data.recent_reports && data.recent_reports.length > 0) {
            let hasNew = false;
            data.recent_reports.forEach(report => {
                if (report.id > highestKnownId) {
                    hasNew = true;
                    highestKnownId = report.id;
                    
                    // Trigger sound & toast notifications
                    if (window.playNotificationSound) window.playNotificationSound();
                    if (window.showToastNotification) {
                        window.showToastNotification(
                            `Laporan Baru: ${report.title}`,
                            `Kategori: ${report.category} | Tingkat Urgensi: ${report.urgency}`,
                            report.urgency,
                            '#'
                        );
                    }
                    if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                        new Notification(`Laporan Baru Masuk!`, {
                            body: `${report.title} di ${report.location_name}`,
                            icon: '♻️'
                        });
                    }
                }
            });
            if (hasNew) {
                // Show a banner urging admin to refresh page to see the grouped layout refresh
                if (window.showToastNotification) {
                    window.showToastNotification(
                        `Pembaruan Halaman`,
                        `Silakan muat ulang halaman (F5) untuk merender data laporan baru pada kategori urgensinya.`,
                        'rendah',
                        'javascript:window.location.reload()'
                    );
                }
            }
        }
    } catch(e) {
        console.warn('Polling error on reports page:', e);
    }
});
</script>
@endpush
