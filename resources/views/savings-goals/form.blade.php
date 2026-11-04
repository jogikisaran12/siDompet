@extends("layouts.app")

@section("title", isset($savingsGoal) ? "Edit Target Tabungan" : "Tambah Target Tabungan")

@section("content")

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card-modern">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon icon-purple mb-0">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">
                        {{ isset($savingsGoal) ? 'Edit Target Tabungan' : 'Target Tabungan Baru' }}
                    </h5>
                    <small class="text-muted">
                        Lengkapi informasi target tabungan Anda
                    </small>
                </div>
            </div>

            <form action="{{ isset($savingsGoal) ? route('savings-goals.update', $savingsGoal) : route('savings-goals.store') }}" method="POST">
                @csrf
                @if(isset($savingsGoal))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-12">
                        <label class="label-modern">Nama Target</label>
                        <input type="text" name="name" class="input-modern" placeholder="Contoh: Membeli Laptop" value="{{ old('name', isset($savingsGoal) ? $savingsGoal->name : '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="label-modern">Nominal Target (Rp)</label>
                        <input type="number" name="target_amount" class="input-modern" min="0.01" step="0.01" placeholder="Contoh: 10000000" value="{{ old('target_amount', isset($savingsGoal) ? $savingsGoal->target_amount : '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="label-modern">Tanggal Target (Opsional)</label>
                        <input type="date" name="target_date" class="input-modern" value="{{ old('target_date', isset($savingsGoal) && $savingsGoal->target_date ? $savingsGoal->target_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-12">
                        <label class="label-modern">Deskripsi (Opsional)</label>
                        <textarea name="description" class="input-modern" rows="4" placeholder="Tambahkan catatan target...">{{ old('description', isset($savingsGoal) ? $savingsGoal->description : '') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ isset($savingsGoal) ? route('savings-goals.show', $savingsGoal) : route('savings-goals.index') }}" class="btn-modern btn-secondary">
                        Batal
                    </a>
                    <button class="btn-modern btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        {{ isset($savingsGoal) ? 'Simpan Perubahan' : 'Buat Target' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
