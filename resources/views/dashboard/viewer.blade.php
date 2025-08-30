<x-app-layout>
  @include('dashboard.partials.theme')

  <x-slot name="header">
    <div class="gg-hero rounded-xl p-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <img src="{{ asset(config('app.brand.logo')) }}" class="h-10 w-auto" alt="Logo">
        <div>
          <h2 class="text-2xl font-semibold tracking-tight">Dashboard Viewer</h2>
          <div class="opacity-90 text-sm">Mode baca – memantau performa tanpa akses ubah data</div>
        </div>
      </div>
      <span class="gg-pill">Viewer</span>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">

    {{-- KPI --}}
    <div class="grid md:grid-cols-4 gap-4">
      <div class="gg-card gg-kpi p-4">
        <div class="text-sm text-gray-500">Total Debitur</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['debitors'] ?? 0,0,',','.') }}</div>
      </div>
      <div class="gg-card gg-kpi p-4">
        <div class="text-sm text-gray-500">Outstanding</div>
        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['outstanding'] ?? 0,0,',','.') }}</div>
      </div>
      <div class="gg-card gg-kpi-2 p-4">
        <div class="text-sm text-gray-500">Pembayaran Bulan Ini</div>
        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['paid_month'] ?? 0,0,',','.') }}</div>
      </div>
      <div class="gg-card gg-kpi-2 p-4">
        <div class="text-sm text-gray-500">Tunggakan</div>
        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['arrears'] ?? 0,0,',','.') }}</div>
      </div>
    </div>

    {{-- Quick links --}}
    <div class="gg-card p-5">
      <div class="flex flex-wrap gap-3">
        <a href="{{ route('debtors.index') }}" class="gg-button">Lihat Debitur</a>
        <a href="{{ route('collections.obligations', ['month'=>now()->month,'year'=>now()->year]) }}" class="gg-button">Kewajiban Bulanan</a>
        <a href="{{ route('payments.index', ['month'=>now()->month,'year'=>now()->year]) }}" class="gg-button">Data Pembayaran</a>
        @if(Route::has('reporting.index'))
          <a href="{{ route('reporting.index') }}" class="gg-button gg-primary">Reporting</a>
        @endif
      </div>
    </div>

  </div>
</x-app-layout>
