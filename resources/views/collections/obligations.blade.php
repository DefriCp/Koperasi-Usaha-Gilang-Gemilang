<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
      Data Collection — Kewajiban
    </h2>
  </x-slot>

  @php
    $monthNames = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
  @endphp

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Filter --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 mb-5">
      <form method="GET" action="{{ route('collections.obligations') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-6">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Bulan</label>
          <select name="month" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            @foreach($monthNames as $i=>$label)
              <option value="{{ $i }}" @selected($i==(int)$month)>{{ $label }}</option>
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

    {{-- Tabel --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-gray-900">
              <th class="px-4 py-3 text-left font-semibold">No</th>
              <th class="px-4 py-3 text-left font-semibold">Kode Mitra</th>
              <th class="px-4 py-3 text-left font-semibold">Nama Mitra</th>
              <th class="px-4 py-3 text-left font-semibold">No. Rekening</th>
              <th class="px-4 py-3 text-left font-semibold">Nama Debitur</th>
              <th class="px-4 py-3 text-right font-semibold">Nominal Angsuran</th>
              <th class="px-4 py-3 text-left font-semibold">Nopen</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
          @php $no=1; @endphp
          @forelse($rows as $r)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-700">{{ $no++ }}</td>
              <td class="px-4 py-3 text-gray-900">{{ $r->loan_number }}</td>
              <td class="px-4 py-3 text-gray-900">{{ $r->project_name }}</td>
              <td class="px-4 py-3 text-gray-900">{{ $r->account_number }}</td>
              <td class="px-4 py-3 text-gray-900">{{ $r->debtor_name }}</td>
              <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format((float)$r->installment,0,',','.') }}</td>
              <td class="px-4 py-3 text-gray-900">{{ $r->nopen }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-10 text-center text-gray-600 font-medium">
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
