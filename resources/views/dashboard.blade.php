@extends("layouts.app")

@section("title", "Dashboard")

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: \"Poppins\", sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Dashboard</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Selamat datang kembali, {{ auth()->user()->name }}!</p>
    </div>
    <div style="text-align: right;">
        <div style="color: var(--text-primary); font-weight: 600;">{{ \Carbon\Carbon::now()->locale("id")->translatedFormat("l, d F Y") }}</div>
        <small style="color: var(--text-secondary);">{{ auth()->user()->name }} - Pengguna Dompetku</small>
    </div>
</div>

<div class="row" style="margin-bottom: 24px; gap: 24px;">
    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-green">
                <i class="bi bi-wallet2"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Saldo Tersedia</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">Rp {{ number_format($balance, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Dapat digunakan</small>
        </div>
    </div>

    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-purple">
                <i class="bi bi-piggy-bank"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Tabungan</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--primary);">Rp {{ number_format($totalSavingsBalance, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Akumulasi seluruh target</small>
        </div>
    </div>

    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Uang Keseluruhan</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">Rp {{ number_format($totalMoney, 0, ",", ".") }}</h3>
            <small style="color: var(--text-secondary);">Saldo tersedia + tabungan</small>
        </div>
    </div>

    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
                <i class="bi bi-receipt"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Jumlah Transaksi</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $transactions->count() }}</h3>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 24px; gap: 24px;">
    <div class="col-lg-6 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-green">
                <i class="bi bi-arrow-down-left"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Pemasukan</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--income);">Rp {{ number_format($income, 0, ",", ".") }}</h3>
        </div>
    </div>

    <div class="col-lg-6 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-red">
                <i class="bi bi-arrow-up-right"></i>
            </div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Pengeluaran</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--expense);">Rp {{ number_format($expense, 0, ",", ".") }}</h3>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;" class="dashboard-grid">
    <div class="card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Grafik Pemasukan & Pengeluaran</h5>
                <small style="color: var(--text-secondary);">12 bulan terakhir</small>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="financeTrendChart"></canvas>
        </div>
    </div>

    <div class="card-modern">
        <div style="margin-bottom: 24px;">
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Pengeluaran per Kategori</h5>
            <small style="color: var(--text-secondary);">Berdasarkan kategori</small>
        </div>
        @if(empty($expenseCategoryLabels))
        <div style="display: flex; align-items: center; justify-content: center; height: 280px; color: var(--text-muted);">
            <div style="text-align: center;">
                <i class="bi bi-pie-chart" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                <p style="margin: 0;">Belum ada data pengeluaran</p>
            </div>
        </div>
        @else
        <div class="chart-container" style="height: 280px;">
            <canvas id="expenseByCategoryChart"></canvas>
        </div>
        @endif
    </div>
</div>

<div class="card-modern">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Transaksi Terbaru</h5>
            <small style="color: var(--text-secondary);">5 transaksi terakhir</small>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route("transactions.index") }}" class="btn-modern btn-outline btn-sm">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
            <a href="{{ route("transactions.create") }}" class="btn-modern btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
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
                    <td>{{ $transaction->transaction_date->format("d M Y") }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $transaction->category->name }}</td>
                    <td>{{ $transaction->description ?? "-" }}</td>
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
                    <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                        Belum ada transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("financeTrendChart");
    if (!canvas || typeof Chart === "undefined") return;

    // Data grafik dikirim dari DashboardController
    const labels = @json($chartLabels);
    const incomeData = @json($chartIncome);
    const expenseData = @json($chartExpense);

    const getCssVar = (name, fallback) => getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;

    let incomeColor = getCssVar("--income", "#16A34A");
    let expenseColor = getCssVar("--expense", "#DC2626");
    let textMutedColor = getCssVar("--text-muted", "#64748B");
    let borderColor = getCssVar("--border", "#E2E8F0");

    const chart = new Chart(canvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Pemasukan",
                    data: incomeData,
                    borderColor: incomeColor,
                    backgroundColor: incomeColor + "14",
                    pointBackgroundColor: incomeColor,
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: "Pengeluaran",
                    data: expenseData,
                    borderColor: expenseColor,
                    backgroundColor: expenseColor + "14",
                    pointBackgroundColor: expenseColor,
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: "index" },
            plugins: {
                legend: { position: "bottom", labels: { usePointStyle: true, padding: 20, color: textMutedColor } },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const amount = context.parsed.y ?? 0;
                            return context.dataset.label + ": " + new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(amount);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function (value) { return new Intl.NumberFormat("id-ID", { notation: "compact", maximumFractionDigits: 1 }).format(value); }, color: textMutedColor },
                    grid: { color: borderColor }
                },
                x: { ticks: { color: textMutedColor }, grid: { color: borderColor } }
            }
        }
    });

    // Grafik ikut menyesuaikan warna saat pengguna berganti tema
    function refreshChartTheme() {
        incomeColor = getCssVar("--income", "#16A34A");
        expenseColor = getCssVar("--expense", "#DC2626");
        textMutedColor = getCssVar("--text-muted", "#64748B");
        borderColor = getCssVar("--border", "#E2E8F0");

        const [incomeDataset, expenseDataset] = chart.data.datasets;

        incomeDataset.borderColor = incomeColor;
        incomeDataset.backgroundColor = incomeColor + "14";
        incomeDataset.pointBackgroundColor = incomeColor;

        expenseDataset.borderColor = expenseColor;
        expenseDataset.backgroundColor = expenseColor + "14";
        expenseDataset.pointBackgroundColor = expenseColor;

        chart.options.plugins.legend.labels.color = textMutedColor;
        chart.options.scales.y.ticks.color = textMutedColor;
        chart.options.scales.y.grid.color = borderColor;
        chart.options.scales.x.ticks.color = textMutedColor;
        chart.options.scales.x.grid.color = borderColor;

        chart.update("none");
    }

    if (window.DompetkuTheme && typeof window.DompetkuTheme.onChange === "function") {
        window.DompetkuTheme.onChange(refreshChartTheme);
    }

    // Grafik Pengeluaran per Kategori
    const expenseCategoryCanvas = document.getElementById("expenseByCategoryChart");
    if (expenseCategoryCanvas && typeof Chart !== "undefined") {
        const categoryLabels = @json($expenseCategoryLabels ?? []);
        const categoryData = @json($expenseCategoryData ?? []);

        if (categoryLabels.length > 0) {
            const palette = [
                getCssVar("--expense", "#DC2626"),
                getCssVar("--warning", "#F59E0B"),
                getCssVar("--primary", "#10B981"),
                getCssVar("--info", "#3B82F6"),
                getCssVar("--purple", "#A855F7"),
                "#EC4899",
                "#14B8A6",
                "#F97316"
            ];

            const categoryColors = categoryLabels.map((_, index) => palette[index % palette.length]);

            const categoryChart = new Chart(expenseCategoryCanvas, {
                type: "doughnut",
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: categoryColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: "bottom", labels: { usePointStyle: true, padding: 16, color: textMutedColor } },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const amount = context.parsed ?? 0;
                                    const label = context.label || "";
                                    return label + ": " + new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(amount);
                                }
                            }
                        }
                    }
                }
            });

            function refreshCategoryTheme() {
                textMutedColor = getCssVar("--text-muted", "#64748B");
                categoryChart.options.plugins.legend.labels.color = textMutedColor;
                categoryChart.update("none");
            }

            if (window.DompetkuTheme && typeof window.DompetkuTheme.onChange === "function") {
                window.DompetkuTheme.onChange(refreshCategoryTheme);
            }
        }
    }
});
</script>

<style>
@media (max-width: 1024px) {
    .dashboard-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .row { gap: 16px; }
}
</style>

@endsection
