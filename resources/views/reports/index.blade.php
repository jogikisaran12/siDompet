@extends("layouts.app")

@section("title", "Laporan Keuangan")

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: \"Poppins\", sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Laporan Keuangan</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Analisis pemasukan dan pengeluaran berdasarkan periode</p>
    </div>
    <a href="{{ route("reports.print", ["month" => $month]) }}" target="_blank" class="btn-modern btn-primary">
        <i class="bi bi-printer"></i> Cetak Laporan
    </a>
</div>

<div class="card-modern" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <div class="stat-icon icon-purple" style="margin-bottom: 0;">
            <i class="bi bi-calendar3"></i>
        </div>
        <div>
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 2px; color: var(--text-primary);">Filter Periode</h5>
            <small style="color: var(--text-secondary);">Pilih bulan dan tahun</small>
        </div>
    </div>
    <form action="{{ route("reports.index") }}" method="GET" class="row g-3">
        <div class="col-md-5">
            <label class="label-modern">Periode</label>
            <input type="month" name="month" class="input-modern" value="{{ $month }}" required>
        </div>
        <div class="col-md-3" style="display: flex; align-items: end;">
            <button type="submit" class="btn-modern btn-primary w-100">
                <i class="bi bi-search"></i> Tampilkan
            </button>
        </div>
        <div class="col-md-3" style="display: flex; align-items: end;">
            <a href="{{ route("reports.index") }}" class="btn-modern btn-secondary w-100">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

<div style="margin-bottom: 24px;">
    <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Ringkasan Keuangan</h5>
    <p style="color: var(--text-secondary); margin: 0;">Periode: {{ \Carbon\Carbon::createFromFormat("!Y-m", $month)->locale("id")->translatedFormat("F Y") }}</p>
</div>

<div class="card-modern" style="margin-bottom: 16px;">
    <div style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary); font-size: 13px;">
        <i class="bi bi-info-circle"></i>
        <span>Aktivitas tabungan dicatat secara terpisah dan tidak dimasukkan ke tabel transaksi utama.</span>
    </div>
</div>

<div class="row" style="gap: 24px; margin-bottom: 24px;">
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div class="stat-icon icon-green" style="margin-bottom: 0;">
                    <i class="bi bi-arrow-down-left"></i>
                </div>
                <span class="badge-income badge-modern">Pemasukan</span>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Pemasukan (Periode)</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0 0 8px; color: var(--income);">Rp {{ number_format($income, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Total uang masuk periode ini</small>
        </div>
    </div>

    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div class="stat-icon icon-red" style="margin-bottom: 0;">
                    <i class="bi bi-arrow-up-right"></i>
                </div>
                <span class="badge-expense badge-modern">Pengeluaran</span>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Pengeluaran (Periode)</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0 0 8px; color: var(--expense);">Rp {{ number_format($expense, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Total uang keluar periode ini</small>
        </div>
    </div>

    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern card-gradient">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 0;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <i class="bi bi-arrow-left-right" style="font-size: 20px; color: white;"></i>
            </div>
            <h6 style="font-size: 13px; color: rgba(255,255,255,0.9); margin-bottom: 8px; font-weight: 600;">Perubahan Saldo Tersedia (Periode)</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0 0 8px; color: white;">Rp {{ number_format($balancePeriodChange, 0, ",", ".") }}</h3>
            <small style="color: rgba(255,255,255,0.9);">Pemasukan - Pengeluaran - Setoran + Penarikan</small>
        </div>
    </div>
</div>

<div class="row" style="gap: 24px; margin-bottom: 24px;">
    <div class="col-lg-6" style="flex: 1;">
        <div class="card-modern">
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Setoran Tabungan (Periode)</h6>
            <h4 style="font-size: 24px; font-weight: 700; margin: 0; color: var(--income);">Rp {{ number_format($depositPeriod, 0, ",", ".") }}</h4>
        </div>
    </div>
    <div class="col-lg-6" style="flex: 1;">
        <div class="card-modern">
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Penarikan Tabungan (Periode)</h6>
            <h4 style="font-size: 24px; font-weight: 700; margin: 0; color: var(--expense);">Rp {{ number_format($withdrawalPeriod, 0, ",", ".") }}</h4>
        </div>
    </div>
</div>

<div class="row" style="gap: 24px; margin-bottom: 24px;">
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon icon-green" style="margin-bottom: 12px;">
                <i class="bi bi-wallet2"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Saldo Tersedia Saat Ini</h6>
            <h3 style="font-size: 24px; font-weight: 700; margin: 0; color: var(--text-primary);">Rp {{ number_format($availableBalanceNow, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Dapat digunakan</small>
        </div>
    </div>
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon icon-purple" style="margin-bottom: 12px;">
                <i class="bi bi-piggy-bank"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Tabungan Saat Ini</h6>
            <h3 style="font-size: 24px; font-weight: 700; margin: 0; color: var(--primary);">Rp {{ number_format($totalSavingsNow, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Akumulasi seluruh target</small>
        </div>
    </div>
    <div class="col-lg-4" style="flex: 1;">
        <div class="card-modern">
            <div class="stat-icon icon-green" style="margin-bottom: 12px;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Uang Keseluruhan</h6>
            <h3 style="font-size: 24px; font-weight: 700; margin: 0; color: var(--text-primary);">Rp {{ number_format($totalMoneyNow, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Saldo tersedia + tabungan</small>
        </div>
    </div>
</div>

<div class="card-modern">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Rincian Transaksi</h5>
            <small style="color: var(--text-secondary);">Periode {{ \Carbon\Carbon::createFromFormat("!Y-m", $month)->locale("id")->translatedFormat("F Y") }}</small>
        </div>
        <span class="badge-neutral badge-modern">{{ $transactions->count() }} Transaksi</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Jenis</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->transaction_date->format("d M Y") }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $transaction->category->name }}</td>
                    <td>{{ $transaction->description ?: "-" }}</td>
                    <td>
                        <span class="{{ $transaction->type == "income" ? "badge-income" : "badge-expense" }} badge-modern">
                            {{ $transaction->type == "income" ? "Pemasukan" : "Pengeluaran" }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 600;" class="{{ $transaction->type == "income" ? "text-success" : "text-danger" }}">
                        {{ $transaction->type == "income" ? "+" : "-" }} Rp {{ number_format($transaction->amount, 0, ",", ".") }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                        Tidak ada transaksi pada periode ini
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
    .col-lg-4 { flex: 1 1 100%; }
}
</style>

@endsection
