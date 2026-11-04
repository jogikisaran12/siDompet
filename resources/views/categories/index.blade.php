@extends("layouts.app")

@section("title", "Kategori")

@section("content")

<div style="margin-bottom: 24px;">
    <h2 style="font-family: \"Poppins\", sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Kategori</h2>
    <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Atur kategori pemasukan dan pengeluaran</p>
</div>

<div class="row" style="gap: 24px;">
    <div class="col-lg-4" style="flex: 0 0 33.333%;">
        <div class="card-modern">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                <div class="stat-icon icon-purple" style="margin-bottom: 0;">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h5 style="font-size: 18px; font-weight: 700; margin: 0; color: var(--text-primary);">Tambah Kategori</h5>
            </div>

            <form action="{{ route("categories.store") }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Nama Kategori</label>
                    <input type="text" name="name" class="input-modern" placeholder="Contoh: Makanan" value="{{ old("name") }}" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="label-modern">Jenis Kategori</label>
                    <select name="type" class="input-modern select-modern">
                        <option value="income" @selected(old("type") == "income")>Pemasukan</option>
                        <option value="expense" @selected(old("type") == "expense")>Pengeluaran</option>
                    </select>
                </div>

                <button class="btn-modern btn-primary w-100">
                    <i class="bi bi-plus-lg"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8" style="flex: 1;">
        <div class="card-modern">
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 24px; color: var(--text-primary);">Daftar Kategori</h5>

            <div style="overflow-x: auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Jenis</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <form id="edit-{{ $category->id }}" action="{{ route("categories.update", $category) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method("PUT")
                                    <input type="text" name="name" value="{{ $category->name }}" class="input-modern" style="max-width: 280px;" required>
                                </form>
                            </td>
                            <td>
                                <span class="{{ $category->type == "income" ? "badge-income" : "badge-expense" }} badge-modern">
                                    {{ $category->type == "income" ? "Pemasukan" : "Pengeluaran" }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button type="submit" form="edit-{{ $category->id }}" class="btn-modern btn-outline btn-sm" title="Simpan">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <form action="{{ route("categories.destroy", $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" style="margin: 0;">
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
                            <td colspan="3" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <i class="bi bi-collection" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                                Belum ada kategori
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 992px) {
    .row { flex-direction: column; }
    .col-lg-4, .col-lg-8 { flex: 1 1 100%; }
}
</style>

@endsection
