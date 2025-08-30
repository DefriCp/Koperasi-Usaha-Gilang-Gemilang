<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
      Reporting — List Outstanding
    </h2>
  </x-slot>

  @php
    $monthNames = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
  @endphp

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 mb-5">
      <form method="GET" action="{{ route('reporting.outstanding') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Bulan</label>
          <select name="month" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            <option value="">— Semua (Jan s/d Des) —</option>
            @foreach($monthNames as $i=>$label)
              <option value="{{ $i }}" @selected(($filters['month'] ?? null)==$i)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Tahun</label>
          <input type="number" name="year" value="{{ $filters['year'] ?? now()->year }}"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>

        <div class="md:col-span-3">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Cabang</label>
          <input type="text" name="branch" value="{{ $filters['branch'] ?? '' }}"
                 placeholder="mis. MAKASSAR"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Area</label>
          <input type="text" name="area" value="{{ $filters['area'] ?? '' }}"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Kuartal</label>
          <select name="quarter" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            <option value="">— (opsional) —</option>
            <option value="1" @selected(($filters['quarter'] ?? null)==1)>Q1</option>
            <option value="2" @selected(($filters['quarter'] ?? null)==2)>Q2</option>
            <option value="3" @selected(($filters['quarter'] ?? null)==3)>Q3</option>
            <option value="4" @selected(($filters['quarter'] ?? null)==4)>Q4</option>
          </select>
        </div>

        <div class="md:col-span-4">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Project</label>
          <select name="project_id" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            <option value="">SEMUA PROJECT</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" @selected(($filters['project'] ?? null)==$p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-3">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Cari (Nopen/Nama)</label>
          <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3">
        </div>

        <div class="md:col-span-3 flex items-end gap-2">
          <button class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Terapkan
          </button>

          <a
            href="{{ route('reporting.outstanding.export', [
                'month'=>$filters['month'] ?? null,
                'year'=>$filters['year'] ?? now()->year,
                'branch'=>$filters['branch'] ?? '',
                'area'=>$filters['area'] ?? '',
                'q'=>$filters['q'] ?? '',
                'project_id'=>$filters['project'] ?? null,
                'quarter'=>$filters['quarter'] ?? null,
            ]) }}"
            class="h-11 px-4 rounded-lg border border-gray-300 text-indigo-600 font-semibold bg-white hover:bg-gray-50">
            Download Excel
          </a>

          <a href="{{ route('reporting.arrears', ['year'=>$filters['year'] ?? now()->year]) }}"
             class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Ke List Menunggak
          </a>
        </div>
      </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
          <tr class="text-gray-900">
            <th class="px-4 py-3 text-left font-semibold">No</th>
            <th class="px-4 py-3 text-left font-semibold">Nopen</th>
            <th class="px-4 py-3 text-left font-semibold">Nama Debitur</th>
            <th class="px-4 py-3 text-right font-semibold">Plafond</th>
            <th class="px-4 py-3 text-right font-semibold">Outstanding</th>
            <th class="px-4 py-3 text-left font-semibold">Tgl Kredit</th>
            <th class="px-4 py-3 text-left font-semibold">Tgl Lunas</th>
            <th class="px-4 py-3 text-left font-semibold">Cabang</th>
            <th class="px-4 py-3 text-left font-semibold">Area</th>
            <th class="px-4 py-3 text-left font-semibold">Project</th>
            <th class="px-4 py-3 text-left font-semibold">Product</th>
            <th class="px-4 py-3 text-left font-semibold">Taspen/Asabri</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
          @php $no=1; @endphp
          @forelse($rows as $r)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ $no++ }}</td>
              <td class="px-4 py-3">{{ $r->nopen }}</td>
              <td class="px-4 py-3">{{ $r->name }}</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($r->plafond,0,',','.') }}</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($r->outstanding,0,',','.') }}</td>
              <td class="px-4 py-3">{{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d/m/Y') : '—' }}</td>
              <td class="px-4 py-3">{{ $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('d/m/Y') : '—' }}</td>
              <td class="px-4 py-3">{{ $r->branch }}</td>
              <td class="px-4 py-3">{{ $r->area }}</td>
              <td class="px-4 py-3">{{ $r->project }}</td>
              <td class="px-4 py-3">{{ $r->product }}</td>
              <td class="px-4 py-3">{{ $r->payer }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="px-4 py-10 text-center text-gray-600 font-medium">
                Tidak ada data untuk filter yang dipilih.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
