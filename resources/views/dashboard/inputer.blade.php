<x-app-layout>
  @include('dashboard.partials.theme')

  <x-slot name="header">
    <div class="gg-hero rounded-xl p-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <img src="{{ asset(config('app.brand.logo')) }}" class="h-10" alt="Logo">
        <div>
          <h2 class="text-2xl font-semibold tracking-tight">Dashboard Inputer</h2>
          <div class="opacity-90 text-sm">Input & pembaruan data operasional</div>
        </div>
      </div>
      <span class="gg-pill">Inputer</span>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">

    {{-- Shortcut --}}
    <div class="gg-card p-5">
      <div class="grid md:grid-cols-3 gap-3">
        <a href="{{ route('debtors.create') }}" class="gg-button gg-primary">+ Tambah Debitur</a>
        <a href="{{ route('debtors.import') }}" class="gg-button">Import Debitur (Excel)</a>
        <a href="{{ route('projects.create') }}" class="gg-button">+ Tambah Project</a>
      </div>
    </div>

    {{-- Aktivitas terakhir --}}
    <div class="gg-card p-5">
      <div class="font-semibold text-gray-900 mb-3">Aktivitas Terakhir</div>
      <div class="text-sm text-gray-600">
        {{-- Tampilkan list aktivitas jika ada; fallback contoh --}}
        @forelse($activities ?? [] as $a)
          <div class="py-2 border-b border-gray-100 flex items-center justify-between">
            <div>{{ $a['text'] }}</div>
            <div class="text-gray-400">{{ $a['time'] }}</div>
          </div>
        @empty
          Belum ada aktivitas terbaru.
        @endforelse
      </div>
    </div>

  </div>
</x-app-layout>
