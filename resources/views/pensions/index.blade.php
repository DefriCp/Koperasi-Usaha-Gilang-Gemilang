<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900">Data Pensiun</h2>
  </x-slot>

  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex gap-2">
      <a href="{{ route('pensions.create') }}"
         class="px-4 h-10 inline-flex items-center rounded-lg border border-gray-300 bg-white font-semibold">
        + Tambah
      </a>
      <a href="{{ route('pensions.import.form') }}"
         class="px-4 h-10 inline-flex items-center rounded-lg border border-gray-300 bg-white font-semibold">
        Import Excel
      </a>
      <a href="{{ route('pensions.template') }}"
         class="px-4 h-10 inline-flex items-center rounded-lg border border-gray-300 bg-white font-semibold">
        Download Template
      </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-gray-900">
            <th class="px-4 py-3 text-left font-semibold">NIP</th>
            <th class="px-4 py-3 text-left font-semibold">Nama</th>
            <th class="px-4 py-3 text-left font-semibold">Cabang</th>
            <th class="px-4 py-3 text-right font-semibold">Bersih</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($rows as $r)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">{{ $r->nip }}</td>
            <td class="px-4 py-3">{{ $r->name }}</td>
            <td class="px-4 py-3">{{ $r->branch_name }}</td>
            <td class="px-4 py-3 text-right">Rp {{ number_format((float)$r->bersih,0,',','.') }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('pensions.edit',$r) }}" class="text-indigo-600 font-semibold">Edit</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-8 text-center text-gray-600">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
</x-app-layout>
