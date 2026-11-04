@extends("layouts.app")

@section("title", "Target Tabungan")

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Target Tabungan</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Kelola target tabungan dan pantau pencapaiannya</p>
    </div>
    <a href="{{ route('savings-goals.create') }}" class="btn-modern btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Target
    </a>
</div>

<div class="card-modern" style="margin-bottom: 16px;">
    <div style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary); font-size: 13px;">
        <i class="bi bi-info-circle"></i>
        <span>Setoran dan penarikan tabungan dicatat terpisah dari transaksi pemasukan/pengeluaran utama.</span>
    </div>
</div>

@if($goals->isEmpty())
<div class="card-modern" style="text-align: center; padding: 48px;">
    <i class="bi bi-piggy-bank" style="font-size: 48px; color: var(--text-muted); display: block; margin-bottom: 12px;"></i>
    <h5 style="color: var(--text-primary); margin-bottom: 8px;">Belum ada target tabungan</h5>
    <p style="color: var(--text-secondary); margin-bottom: 16px;">Buat target pertamamu untuk mulai menabung.</p>
    <a href="{{ route('savings-goals.create') }}" class="btn-modern btn-primary">
        <i class="bi bi-plus-lg"></i> Buat Target Tabungan
    </a>
</div>
@else
<div class="row" style="gap: 24px;">
    @foreach($goals as $goal)
    <div class="col-lg-4 col-md-6" style="flex: 1 1 30%;">
        <div class="card-modern">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">{{ $goal->name }}</h5>
                    @if($goal->target_date)
                    <small style="color: var(--text-secondary);"><i class="bi bi-calendar-event"></i> Target: {{ $goal->target_date->format('d M Y') }}</small>
                    @endif
                </div>
                <a href="{{ route('savings-goals.show', $goal) }}" class="btn-modern btn-outline btn-sm">
                    <i class="bi bi-eye"></i> Detail
                </a>
            </div>

            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: var(--text-secondary); font-size: 13px;">Terkumpul</span>
                    <span style="color: var(--text-primary); font-weight: 600;">Rp {{ number_format((float)$goal->balance, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 13px;">Target</span>
                    <span style="color: var(--text-primary); font-weight: 600;">Rp {{ number_format((float)$goal->target_amount, 0, ',', '.') }}</span>
                </div>
                <div style="width: 100%; height: 10px; background: var(--border); border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; background: var(--primary); width: {{ min($goal->progressDisplay, 100) }}%;"></div>
                </div>
                <div style="text-align: right; margin-top: 6px;">
                    <span style="color: var(--primary); font-weight: 600; font-size: 13px;">{{ number_format($goal->progressDisplay, 0) }}%</span>
                </div>
            </div>

            @if($goal->description)
            <p style="color: var(--text-secondary); font-size: 13px; margin: 0;">{{ Str::limit($goal->description, 80) }}</p>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

<style>
@media (max-width: 992px) {
    .row { flex-direction: column; }
    .col-lg-4, .col-md-6 { flex: 1 1 100%; }
}
</style>

@endsection
