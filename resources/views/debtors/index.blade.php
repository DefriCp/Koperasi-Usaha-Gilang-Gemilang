<x-app-layout>
  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Debitur</h2>

      @role('inputer|checker')
      <div class="flex gap-2">
        <a href="{{ route('debtors.import') }}"
           class="inline-flex items-center h-10 px-4 rounded-lg border border-blue-600 text-blue-700 font-semibold bg-white hover:bg-blue-50">
          Import Excel
        </a>
        <a href="{{ route('debtors.create') }}"
           class="inline-flex items-center h-10 px-4 rounded-lg border border-emerald-600 text-emerald-700 font-semibold bg-white hover:bg-emerald-50">
          Tambah Debitur
        </a>
      </div>
      @endrole
    </div>
  </x-slot>

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    {{-- ALERTS --}}
    @if (session('ok') || session('last_import_batch'))
      <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 flex items-center justify-between gap-3">
        <div class="font-medium">
          {!! session('ok') !!}
        </div>

        @if (session('last_import_batch'))
          <form method="POST" action="{{ route('debtors.import.rollback', session('last_import_batch')) }}">
            @csrf @method('DELETE')
            <button type="submit"
              class="h-9 px-3 rounded-md border border-rose-500 text-rose-700 bg-white hover:bg-rose-50 font-semibold">
              Undo import
            </button>
          </form>
        @endif
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        <ul class="list-disc ms-5">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- FILTER --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 mb-5">
      <form method="GET" action="{{ route('debtors.index') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-6">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Cari (Nopen / Nama)</label>
          <input type="text" name="search" value="{{ request('search') }}"
                 class="h-11 w-full rounded-lg border border-gray-300 px-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                 placeholder="ketik nopen atau nama…">
        </div>
        <div class="md:col-span-4">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Project</label>
          <select name="project"
                  class="h-11 w-full rounded-lg border border-gray-300 px-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">— Semua Project —</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" @selected(request('project')==$p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-2 flex items-end gap-2">
          <button class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Terapkan
          </button>
          <a href="{{ route('debtors.index') }}"
             class="h-11 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
            Reset
          </a>
        </div>
      </form>
    </div>

    {{-- TABEL --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-gray-900">
              <th class="px-4 py-3 text-left font-semibold">No</th>
              <th class="px-4 py-3 text-left font-semibold">No Pensiun</th>
              <th class="px-4 py-3 text-left font-semibold">Nama Penerima</th>
              <th class="px-4 py-3 text-left font-semibold">Project</th>
              <th class="px-4 py-3 text-right font-semibold">Plafon</th>
              <th class="px-4 py-3 text-right font-semibold">Angsuran / bln</th>
              <th class="px-4 py-3 text-center font-semibold">Tenor</th>
              <th class="px-4 py-3 text-center font-semibold">Angs ke</th>
              <th class="px-4 py-3 text-center font-semibold">Status</th>
              <th class="px-4 py-3 text-center font-semibold">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            @forelse($debtors as $i => $d)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-700">{{ $debtors->firstItem() + $i }}</td>

                <td class="px-4 py-3 font-mono">
                  <a href="{{ route('debtors.show',$d) }}"
                     class="text-indigo-700 font-semibold hover:underline">
                    {{ $d->nopen }}
                  </a>
                </td>

                <td class="px-4 py-3">
                  <a href="{{ route('debtors.show',$d) }}"
                     class="text-gray-900 font-semibold hover:underline">
                    {{ $d->name }}
                  </a>
                </td>

                <td class="px-4 py-3 text-gray-900">{{ $d->project?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format((float)$d->plafond, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format((float)$d->installment, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center text-gray-900">{{ $d->tenor }}</td>
                <td class="px-4 py-3 text-center text-gray-900">{{ $d->installment_no }}</td>

                <td class="px-4 py-3 text-center">
                  @php
                    $badge = [
                      'pending'  => 'bg-amber-100 text-amber-800 border-amber-300',
                      'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                      'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                    ][$d->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                  @endphp
                  <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase border {{ $badge }}">
                    {{ strtoupper($d->status) }}
                  </span>
                </td>

                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-2">
                    @role('checker')
                      @if ($d->status === 'pending')
                        <form method="POST" action="{{ route('debtors.approve', $d) }}">
                          @csrf
                          <input type="hidden" name="decision" value="approved">
                          <button
                            class="px-3 py-1.5 rounded-md border border-emerald-600 text-emerald-700 bg-white text-xs font-semibold hover:bg-emerald-50">
                            Approve
                          </button>
                        </form>
                        <form method="POST" action="{{ route('debtors.approve', $d) }}">
                          @csrf
                          <input type="hidden" name="decision" value="rejected">
                          <button
                            class="px-3 py-1.5 rounded-md border border-rose-600 text-rose-700 bg-white text-xs font-semibold hover:bg-rose-50">
                            Reject
                          </button>
                        </form>
                      @endif
                    @endrole

                    {{-- Tombol Hapus untuk checker & inputer --}}
                    @hasanyrole('checker|inputer')
                      <form method="POST" action="{{ route('debtors.destroy', $d) }}"
                            onsubmit="return confirm('Hapus debitur {{ $d->name }} ({{ $d->nopen }})? Data repayment juga akan dihapus.');">
                        @csrf
                        @method('DELETE')
                        <button
                          class="px-3 py-1.5 rounded-md border border-rose-600 text-rose-700 bg-white text-xs font-semibold hover:bg-rose-50">
                          Hapus
                        </button>
                      </form>
                    @endhasanyrole
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="px-4 py-10 text-center text-gray-600 font-medium">
                  Belum ada data.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t border-gray-100">
        {{ $debtors->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
