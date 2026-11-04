@extends("layouts.app")

@section("title", $savingsGoal->name)

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">{{ $savingsGoal->name }}</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Detail target tabungan</p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('savings-goals.index') }}" class="btn-modern btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('savings-goals.edit', $savingsGoal) }}" class="btn-modern btn-outline">
            <i class="bi bi-pencil-square"></i> Edit
        </a>
        <form action="{{ route('savings-goals.destroy', $savingsGoal) }}" method="POST" onsubmit="return confirm('Menghapus target ini akan menghapus SELURUH riwayat setoran dan penarikan pada target ini. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')">
            @csrf
            @method('DELETE')
            <button class="btn-modern btn-danger">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

<div class="card-modern" style="margin-bottom: 16px;">
    <div style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary); font-size: 13px;">
        <i class="bi bi-info-circle"></i>
        <span>Setoran dan penarikan tabungan dicatat terpisah dari transaksi pemasukan/pengeluaran utama.</span>
    </div>
</div>

<div class="row" style="gap: 24px; margin-bottom: 24px;">
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon icon-green" style="margin-bottom: 16px;">
                <i class="bi bi-wallet2"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Saldo Terkumpul</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--income);">Rp {{ number_format((float)$balance, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon icon-purple" style="margin-bottom: 16px;">
                <i class="bi bi-bullseye"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Target Tabungan</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">Rp {{ number_format((float)$savingsGoal->target_amount, 0, ',', '.') }}</h3>
            @if($savingsGoal->target_date)
            <small style="color: var(--text-secondary);">Target tanggal: {{ $savingsGoal->target_date->format('d M Y') }}</small>
            @endif
        </div>
    </div>
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon" style="background: var(--primary-light); color: var(--primary); margin-bottom: 16px;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Pencapaian</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0 0 12px; color: var(--primary);">{{ number_format($progressDisplay, 0) }}%</h3>
            <div style="width: 100%; height: 10px; background: var(--border); border-radius: 999px; overflow: hidden;">
                <div style="height: 100%; background: var(--primary); width: {{ $progressDisplay }}%;"></div>
            </div>
        </div>
    </div>
</div>

@if($savingsGoal->description)
<div class="card-modern" style="margin-bottom: 24px;">
    <h6 style="font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Deskripsi</h6>
    <p style="color: var(--text-secondary); margin: 0;">{{ $savingsGoal->description }}</p>
</div>
@endif

<div class="row" style="gap: 24px; margin-bottom: 24px;">
    <div class="col-lg-6" style="flex: 1;">
        <div class="card-modern">
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 16px; color: var(--text-primary);"><i class="bi bi-arrow-down-circle text-success"></i> Tambah Setoran</h5>
            <form action="{{ route('savings-goals.deposit', $savingsGoal) }}" method="POST">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label class="label-modern">Nominal Setoran (Rp)</label>
                    <input type="number" name="amount" class="input-modern" min="0.01" step="0.01" placeholder="Contoh: 500000" required>
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Catatan (Opsional)</label>
                    <input type="text" name="note" class="input-modern" placeholder="Catatan setoran">
                </div>
                <button class="btn-modern btn-primary w-100">
                    <i class="bi bi-plus-lg"></i> Tambah Setoran
                </button>
            </form>
        </div>
    </div>
    <div class="col-lg-6" style="flex: 1;">
        <div class="card-modern">
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 16px; color: var(--text-primary);"><i class="bi bi-arrow-up-circle text-danger"></i> Lakukan Penarikan</h5>
            <form action="{{ route('savings-goals.withdrawal', $savingsGoal) }}" method="POST">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label class="label-modern">Nominal Penarikan (Rp)</label>
                    <input type="number" name="amount" class="input-modern" min="0.01" step="0.01" placeholder="Contoh: 200000" required>
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Catatan (Opsional)</label>
                    <input type="text" name="note" class="input-modern" placeholder="Catatan penarikan">
                </div>
                <button class="btn-modern btn-danger w-100">
                    <i class="bi bi-dash-lg"></i> Tarik Dana
                </button>
            </form>
            <small style="color: var(--text-secondary); display: block; margin-top: 8px;">Penarikan tidak boleh melebihi saldo tabungan.</small>
        </div>
    </div>
</div>

<div class="card-modern">
    <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 16px; color: var(--text-primary);">Riwayat Setoran & Penarikan</h5>
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="{{ $entry->type == 'deposit' ? 'badge-income' : 'badge-expense' }} badge-modern">
                            {{ $entry->type == 'deposit' ? 'Setoran' : 'Penarikan' }}
                        </span>
                    </td>
                    <td style="font-weight: 600;" class="{{ $entry->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                        {{ $entry->type == 'deposit' ? '+' : '-' }} Rp {{ number_format((float)$entry->amount, 0, ',', '.') }}
                    </td>
                    <td>{{ $entry->note ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 32px; color: var(--text-muted);">
                        Belum ada riwayat setoran atau penarikan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@media (max-width: 992px) {
    .row { flex-direction: column; }
    .col-lg-4, .col-lg-6 { flex: 1 1 100%; }
}
</style>

@endsection
