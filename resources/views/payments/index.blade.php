<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
        Payments
      </h2>

      <a href="{{ route('payments.export', ['month'=>request('month', $month), 'year'=>request('year', $year)]) }}"
         class="inline-flex items-center h-10 px-4 rounded-lg border border-emerald-600 text-emerald-700 font-semibold bg-white hover:bg-emerald-50">
        Download Excel
      </a>
    </div>
  </x-slot>

  @php
    $monthNames = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
  @endphp

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 mb-5">
      <form method="GET" action="{{ route('payments.index') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-6">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Bulan</label>
          <select name="month" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            @foreach($monthNames as $i => $label)
              <option value="{{ $i }}" @selected($i==(int)($month))>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-4">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Tahun</label>
          <input type="number" name="year" value="{{ $year }}"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>
        <div class="md:col-span-2 flex items-end">
          <button class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Terapkan
          </button>
        </div>
      </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-gray-900">
              <th class="px-4 py-3 text-left font-semibold">id</th>
              <th class="px-4 py-3 text-left font-semibold">tgl_efekt</th>
              <th class="px-4 py-3 text-left font-semibold">batch</th>
              <th class="px-4 py-3 text-left font-semibold">kode_mitra</th>
              <th class="px-4 py-3 text-left font-semibold">nama_mitra</th>
              <th class="px-4 py-3 text-left font-semibold">no_rekening</th>
              <th class="px-4 py-3 text-left font-semibold">nama</th>
              <th class="px-4 py-3 text-left font-semibold">nopen</th>
              <th class="px-4 py-3 text-right font-semibold">nominal</th>
              <th class="px-4 py-3 text-left font-semibold">status</th>
              <th class="px-4 py-3 text-left font-semibold">tgl_debet</th>
              <th class="px-4 py-3 text-left font-semibold">keterangan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($rows as $r)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $r->id }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->tgl_efekt }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->batch }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->kode_mitra ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->nama_mitra }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->no_rekening ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->nama }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->nopen }}</td>
                <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format($r->nominal,0,',','.') }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->status }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->tgl_debet }}</td>
                <td class="px-4 py-3 text-gray-900">{{ $r->keterangan }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="px-4 py-10 text-center text-gray-600 font-medium">
                  Tidak ada data untuk {{ $monthNames[(int)$month] }} {{ $year }}.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
