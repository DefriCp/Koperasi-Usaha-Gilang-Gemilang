<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Payments</h2>
      <a href="{{ route('payments.export', ['month'=>request('month', $month), 'year'=>request('year', $year), 'q'=>request('q',$q??'')]) }}"
         class="inline-flex items-center h-10 px-4 rounded-lg border border-emerald-600 text-emerald-700 font-semibold bg-white hover:bg-emerald-50">
        Download Excel
      </a>
    </div>
  </x-slot>

  @php
    $monthNames = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
    $money = fn($n) => 'Rp '.number_format((float)$n,0,',','.');
    $statusOptions = [
      'lunas'         => 'Lunas',
      'dalam_proses'  => 'Dalam Proses',
      'menunggak'     => 'Menunggak',
    ];
    $mLabel = in_array(strtolower($month),['all','semua']) ? 'Semua Bulan' : $monthNames[(int)$month];
  @endphp

  <div class="py-8 max-w-[1400px] mx-auto sm:px-6 lg:px-8 space-y-5">
    @if (session('ok'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('ok') }}</div>
    @endif

    {{-- FILTER --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
      <form method="GET" action="{{ route('payments.index') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-5">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Bulan</label>
          <select name="month" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            <option value="all" @selected(strtolower($month)==='all')>Semua Bulan</option>
            @foreach($monthNames as $i=>$label)
              <option value="{{ $i }}" @selected((string)$i===(string)$month)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Tahun</label>
          <input type="number" name="year" value="{{ $year }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>
        <div class="md:col-span-4">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Cari (Nama / Nopen)</label>
          <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="ketik nama atau nopen…"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>
        <div class="md:col-span-12 flex items-end">
          <button class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Terapkan
          </button>
        </div>
      </form>
    </div>

    {{-- TABEL --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-auto">
          {{-- Atur lebar kolom agar proporsional (total = 100%) --}}
          <colgroup>
            <col style="width:3%">   {{-- id --}}
            <col style="width:7%">   {{-- tgl_efekt --}}
            <col style="width:9%">   {{-- batch --}}
            <col style="width:7%">   {{-- kode_mitra --}}
            <col style="width:9%">   {{-- nama_mitra --}}
            <col style="width:7%">   {{-- no_rekening --}}
            <col style="width:13%">  {{-- nama --}}
            <col style="width:8%">   {{-- nopen --}}
            <col style="width:8%">   {{-- nominal --}}
            <col style="width:10%">  {{-- status (dibesarkan) --}}
            <col style="width:8%">   {{-- tgl_debet --}}
            <col style="width:9%">   {{-- keterangan --}}
            <col style="width:2%">   {{-- aksi --}}
          </colgroup>

          <thead class="bg-gray-50">
            <tr class="text-gray-900">
              <th class="px-3 py-3 text-left font-semibold">id</th>
              <th class="px-3 py-3 text-left font-semibold">tgl_efekt</th>
              <th class="px-3 py-3 text-left font-semibold">batch</th>
              <th class="px-3 py-3 text-left font-semibold">kode_mitra</th>
              <th class="px-3 py-3 text-left font-semibold">nama_mitra</th>
              <th class="px-3 py-3 text-left font-semibold">no_rekening</th>
              <th class="px-3 py-3 text-left font-semibold">nama</th>
              <th class="px-3 py-3 text-left font-semibold">nopen</th>
              <th class="px-3 py-3 text-right font-semibold">nominal</th>
              <th class="px-3 py-3 text-left font-semibold">status</th>
              <th class="px-3 py-3 text-left font-semibold">tgl_debet</th>
              <th class="px-3 py-3 text-left font-semibold">keterangan</th>
              <th class="px-3 py-3 text-center font-semibold">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            @forelse($rows as $r)
              <tr class="hover:bg-gray-50 align-top">
                <td class="px-3 py-3 text-gray-700">{{ $r->id }}</td>
                <td class="px-3 py-3 text-gray-900 whitespace-nowrap">{{ $r->tgl_efekt }}</td>
                <td class="px-3 py-3 text-gray-900">{{ $r->batch }}</td>
                <td class="px-3 py-3 text-gray-900">{{ $r->kode_mitra ?: '—' }}</td>
                <td class="px-3 py-3 text-gray-900">{{ $r->nama_mitra }}</td>
                <td class="px-3 py-3 text-gray-900 whitespace-nowrap">{{ $r->no_rekening ?: '—' }}</td>
                <td class="px-3 py-3 text-gray-900">{{ $r->nama }}</td>
                <td class="px-3 py-3 text-gray-900 whitespace-nowrap">{{ $r->nopen }}</td>
                <td class="px-3 py-3 text-right text-gray-900 whitespace-nowrap">{{ $money($r->nominal) }}</td>

                {{-- kolom edit --}}
                <td class="px-3 py-2">
                  @role('inputer|checker')
                  <form method="POST" action="{{ route('repayments.update', $r->id) }}" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="month" value="{{ request('month', $month) }}">
                    <input type="hidden" name="year"  value="{{ request('year', $year) }}">
                    <input type="hidden" name="q"     value="{{ request('q', $q ?? '') }}">

                    {{-- dibuat lebih lebar + min-width agar teks status tampil penuh --}}
                    <select name="status" class="h-9 w-full min-w-[140px] rounded-md border border-gray-300 px-2 text-sm">
                      @foreach($statusOptions as $val=>$label)
                        <option value="{{ $val }}" @selected($r->status_key === $val)>{{ $label }}</option>
                      @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="date" name="paid_date" value="{{ $r->tgl_debet_inp }}"
                           class="h-9 w-full min-w-[140px] rounded-md border border-gray-300 px-2 text-sm">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="keterangan" value="{{ $r->keterangan_val }}" placeholder="tulis keterangan…"
                           class="h-9 w-full rounded-md border border-gray-300 px-3 text-sm">
                </td>
                <td class="px-3 py-2 text-center">
                    <button class="h-9 px-3 rounded-md border border-indigo-600 text-indigo-700 font-semibold bg-white hover:bg-indigo-50">
                      Simpan
                    </button>
                  </form>
                  @else
                    <span class="px-2 py-1 rounded-full text-xs font-semibold border
                      {{ $r->status_key==='lunas' ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : ($r->status_key==='dalam_proses' ? 'bg-amber-50 text-amber-700 border-amber-200'
                                                           : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                      {{ ucfirst(str_replace('_',' ', $r->status_key)) }}
                    </span>
                  @endrole
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" class="px-4 py-10 text-center text-gray-600 font-medium">
                  Tidak ada data untuk {{ $mLabel }} {{ $year }}.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
