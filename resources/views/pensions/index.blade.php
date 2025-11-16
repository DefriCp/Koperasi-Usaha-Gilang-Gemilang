{{-- resources/views/pensions/index.blade.php --}}
<x-app-layout>
  {{-- ===== Header ===== --}}
  <x-slot name="header">
    <div class="flex flex-col gap-1">
      <h2 class="font-bold text-2xl text-gray-900">Data Pensiun</h2>
      <p class="text-sm text-gray-500">
        @if(request()->filled('q'))
          Hasil untuk <span class="font-semibold text-gray-800">“{{ request('q') }}”</span> —
        @endif
        total <span class="font-semibold text-gray-800">{{ number_format($rows->total()) }}</span> data
      </p>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    {{-- ===== Actions + Search ===== --}}
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('pensions.create') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-800 font-semibold shadow-sm hover:bg-gray-50">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Tambah
        </a>
        <a href="{{ route('pensions.import.form') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-800 font-semibold shadow-sm hover:bg-gray-50">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16M8 8h8M8 12h5"/></svg>
          Import Excel
        </a>
      </div>

      {{-- Search (khusus NIP) --}}
      <form method="GET" action="{{ route('pensions.index') }}" class="w-full md:w-[420px]">
        <div class="relative">
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari NIP…"
            class="w-full h-11 pl-11 pr-10 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
          />
          <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>

          @if(request()->filled('q'))
            <a href="{{ route('pensions.index') }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
               title="Reset pencarian">&times;</a>
          @endif
        </div>

        {{-- Pertahankan parameter lain --}}
        @foreach(request()->except(['page']) as $k => $v)
          @if($k !== 'q')
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endif
        @endforeach
      </form>
    </div>

    {{-- ===== Card Tabel ===== --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="px-4 sm:px-6 py-3 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Menampilkan <span class="font-semibold text-gray-800">{{ number_format($rows->count()) }}</span> baris pada halaman ini
        </div>
        @if(request()->filled('q'))
          <span class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
            Kata kunci: <strong class="font-semibold">{{ request('q') }}</strong>
          </span>
        @endif
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-[520px] w-full text-sm">
          <thead class="bg-white sticky top-0 z-10 shadow-sm">
            <tr class="text-gray-900">
              <th class="px-4 sm:px-6 py-3 text-left font-semibold">NIP</th>
              <th class="px-4 sm:px-6 py-3 text-right font-semibold w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($rows as $r)
              <tr class="hover:bg-indigo-50/40 transition-all duration-150">
                <td class="px-4 sm:px-6 py-3">
                  <a href="{{ route('pensions.show', $r) }}"
                     class="font-semibold text-indigo-700 hover:text-indigo-800 hover:underline">
                    {{ $r->nip }}
                  </a>
                </td>
                <td class="px-4 sm:px-6 py-3 text-right">
                  <a href="{{ route('pensions.edit',$r) }}"
                     class="inline-flex items-center gap-1 text-indigo-700 hover:text-indigo-900 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536M4 13.5V20h6.5l8.5-8.5a2 2 0 00-2.828-2.828L7.672 17.172 4 13.5z"/>
                    </svg>
                    Edit
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="px-6 py-12">
                  <div class="flex flex-col items-center justify-center text-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-gray-100 grid place-items-center text-gray-400">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                      </svg>
                    </div>
                    <div class="text-gray-700 font-semibold">Belum ada data</div>
                    <div class="text-gray-500 text-sm">
                      @if(request()->filled('q'))
                        Tidak ada yang cocok dengan kata kunci.
                        <a href="{{ route('pensions.index') }}" class="text-indigo-700 hover:underline font-medium">Reset pencarian</a>
                      @else
                        Mulai dengan menambahkan data pensiun terlebih dahulu.
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Footer table --}}
      <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/70 flex items-center justify-between">
        <div class="text-xs sm:text-sm text-gray-600">
          Halaman <span class="font-semibold text-gray-800">{{ $rows->currentPage() }}</span>
          dari <span class="font-semibold text-gray-800">{{ $rows->lastPage() }}</span>
        </div>
        <div>
          {{ $rows->appends(request()->except('page'))->links() }}
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Small extras (opsional) ===== --}}
  <style>
    .overflow-x-auto::-webkit-scrollbar{ height:10px }
    .overflow-x-auto::-webkit-scrollbar-thumb{
      background:#e5e7eb; border-radius:9999px; border:3px solid #fff;
    }
    .overflow-x-auto:hover::-webkit-scrollbar-thumb{ background:#d1d5db }
  </style>
</x-app-layout>
