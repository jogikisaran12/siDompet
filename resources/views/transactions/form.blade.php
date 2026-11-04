
@extends('layouts.app')

@section('title', isset($transaction) ? 'Edit Transaksi' : 'Tambah Transaksi')

@section('content')

@php
    $editing = isset($transaction);
    $selectedType = old('type', $editing ? $transaction->type : 'expense');
    $selectedCategory = old('category_id', $editing ? $transaction->category_id : '');
@endphp

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card-modern">

            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon icon-purple mb-0">
                    <i class="bi bi-wallet2"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        {{ $editing ? 'Edit Transaksi' : 'Transaksi Baru' }}
                    </h5>
                    <small class="text-muted">
                        Lengkapi informasi keuangan di bawah ini.
                    </small>
                </div>
            </div>

            <form action="{{ $editing ? route('transactions.update', $transaction) : route('transactions.store') }}"
                  method="POST">
                @csrf

                @if($editing)
                    @method('PUT')
                @endif

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="label-modern">Jenis Transaksi</label>
                        <select name="type" id="type" class="input-modern select-modern" required>
                            <option value="income" @selected($selectedType == 'income')>
                                Pemasukan
                            </option>
                            <option value="expense" @selected($selectedType == 'expense')>
                                Pengeluaran
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="label-modern">Kategori</label>
                        <select name="category_id" id="category_id"
                                class="input-modern select-modern" required>
                            <option value="">Pilih Kategori</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-type="{{ $category->type }}"
                                        @selected($selectedCategory == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="label-modern">Jumlah Transaksi (Rp)</label>
                        <input type="number" name="amount"
                               class="input-modern"
                               min="0.01" max="9999999999999.99" step="0.01"
                               placeholder="Contoh: 50000"
                               value="{{ old('amount', $editing ? $transaction->amount : '') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="label-modern">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date"
                               class="input-modern"
                               value="{{ old('transaction_date', $editing ? $transaction->transaction_date->format('Y-m-d') : date('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-12">
                        <label class="label-modern">Keterangan</label>
                        <textarea name="description" class="input-modern"
                                  rows="4"
                                  placeholder="Tambahkan catatan transaksi...">{{ old('description', $editing ? $transaction->description : '') }}</textarea>
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('transactions.index') }}"
                       class="btn-modern btn-secondary">
                        Batal
                    </a>

                    <button class="btn-modern btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        {{ $editing ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    const typeInput = document.getElementById('type');
    const categoryInput = document.getElementById('category_id');

    function filterCategories() {
        const selectedType = typeInput.value;

        Array.from(categoryInput.options).forEach(option => {
            if (!option.value) return;

            const matches = option.dataset.type === selectedType;

            option.hidden = !matches;
            option.disabled = !matches;
        });

        const selected = categoryInput.selectedOptions[0];

        if (selected && selected.disabled) {
            categoryInput.value = '';
        }
    }

    typeInput.addEventListener('change', filterCategories);
    filterCategories();
</script>

@endsection
