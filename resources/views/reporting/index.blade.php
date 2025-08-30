<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
      Reporting
    </h2>
  </x-slot>

  <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
    <div class="grid md:grid-cols-2 gap-6">
      {{-- Card: List Outstanding --}}
      <div class="rounded-xl bg-white border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">List Outstanding</h3>
        <p class="text-gray-600 mb-4">
          Laporan outstanding debitur dengan filter bulan/tahun, cabang, area, kuartal, dan project.
        </p>
        <a
          href="{{ route('reporting.outstanding', ['year' => now()->year]) }}"
          class="inline-flex items-center h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
          Buka Laporan
        </a>
      </div>

      {{-- Card: Cari Debitur (opsional → arahkan ke daftar Debitur) --}}
      <div class="rounded-xl bg-white border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cari Debitur</h3>
        <p class="text-gray-600 mb-4">
          Cari debitur berdasarkan Nopen atau Nama. Anda akan diarahkan ke halaman Debitur.
        </p>
        <form method="GET" action="{{ route('debtors.index') }}" class="flex gap-3">
          <input
            type="text"
            name="search"
            placeholder="Nopen / Nama"
            class="h-11 flex-1 rounded-lg border border-gray-300 px-3"
          >
          <button
            class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Cari
          </button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
