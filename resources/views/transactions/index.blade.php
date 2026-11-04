@extends("layouts.app")

@section("title", "Transaksi")

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: \"Poppins\", sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Transaksi</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Riwayat dan pengelolaan transaksi keuangan</p>
    </div>
    <a href="{{ route("transactions.create") }}" class="btn-modern btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Transaksi
    </a>
</div>

<div class="card-modern">
    <form method="GET" action="{{ route("transactions.index") }}" class="row g-3" style="margin-bottom: 24px;">
        <div class="col-md-4">
            <label class="label-modern">Jenis Transaksi</label>
            <select name="type" class="input-modern select-modern">
                <option value="">Semua Transaksi</option>
                <option value="income" @selected(request("type") == "income")>Pemasukan</option>
                <option value="expense" @selected(request("type") == "expense")>Pengeluaran</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="label-modern">Periode</label>
            <input type="month" name="month" class="input-modern" value="{{ request("month") }}">
        </div>

        <div class="col-md-4" style="display: flex; align-items: end; gap: 12px;">
            <button class="btn-modern btn-primary">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route("transactions.index") }}" class="btn-modern btn-secondary">
                Reset
            </a>
        </div>
    </form>

    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format("d M Y") }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $transaction->category->name }}</td>
                    <td>
                        <span class="{{ $transaction->type == "income" ? "badge-income" : "badge-expense" }} badge-modern">
                            {{ $transaction->type == "income" ? "Pemasukan" : "Pengeluaran" }}
                        </span>
                    </td>
                    <td>{{ $transaction->description ?? "-" }}</td>
                    <td style="font-weight: 600;" class="{{ $transaction->type == "income" ? "text-success" : "text-danger" }}">
                        {{ $transaction->type == "income" ? "+" : "-" }} Rp {{ number_format($transaction->amount, 0, ",", ".") }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route("transactions.edit", $transaction) }}" class="btn-modern btn-outline btn-sm" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route("transactions.destroy", $transaction) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi?')">
                                @csrf
                                @method("DELETE")
                                <button class="btn-modern btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                        Belum ada transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div style="margin-top: 24px; display: flex; justify-content: center;">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

@endsection
