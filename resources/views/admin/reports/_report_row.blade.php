<tr data-id="{{ $report->id }}" data-status="{{ $report->status }}" class="report-row">
    <td style="padding-left:20px">
        @if($report->image_path)
            <img
                src="{{ $report->image_url }}"
                class="report-image"
                alt="Foto laporan"
                title="{{ $report->image_path }}"
                onclick="openLightbox(this.src)"
                onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <div class="img-placeholder" style="display:none">📷</div>
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
