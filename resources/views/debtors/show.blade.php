{{-- resources/views/debtors/show.blade.php --}}
<x-app-layout>
  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
          {{ $debtor->name }}
        </h2>
        <div class="text-sm text-gray-500 mt-1">
          Nopen: <span class="font-mono">{{ $debtor->nopen }}</span> ·
          Project: {{ $debtor->project?->name ?? '—' }}
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('debtors.index') }}"
           class="h-10 px-4 rounded-lg border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50">
          Kembali
        </a>

        <a href="{{ route('debtors.schedule.print', $debtor) }}"
           class="h-10 px-4 rounded-lg border border-indigo-600 text-indigo-700 font-semibold bg-white hover:bg-indigo-50">
          Cetak Jadwal
        </a>

        @role('checker')
          @if($debtor->status === 'pending')
            <form method="POST" action="{{ route('debtors.approve', $debtor) }}" class="inline">
              @csrf
              <input type="hidden" name="decision" value="approved">
              <button class="h-10 px-4 rounded-lg border border-emerald-600 text-emerald-700 font-semibold bg-white hover:bg-emerald-50">
                Approve
              </button>
            </form>
            <form method="POST" action="{{ route('debtors.approve', $debtor) }}" class="inline">
              @csrf
              <input type="hidden" name="decision" value="rejected">
              <button class="h-10 px-4 rounded-lg border border-rose-600 text-rose-700 font-semibold bg-white hover:bg-rose-50">
                Reject
              </button>
            </form>
          @endif
        @endrole

        @role('inputer|checker')
          {{-- Hapus: inputer hanya bisa hapus miliknya & status pending (dibatasi di controller) --}}
          <form method="POST" action="{{ route('debtors.destroy', $debtor) }}"
                onsubmit="return confirm('Yakin ingin menghapus debitur ini? Tindakan tidak dapat dibatalkan.');">
            @csrf @method('DELETE')
            <button class="h-10 px-4 rounded-lg border border-red-600 text-red-700 font-semibold bg-white hover:bg-red-50">
              Hapus
            </button>
          </form>
        @endrole
      </div>
    </div>
  </x-slot>

  @php
    $money = fn($n) => 'Rp '.number_format((float)$n, 0, ',', '.');
    $badge = [
      'pending'  => 'bg-amber-100 text-amber-800 border-amber-300',
      'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
      'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
    ][$debtor->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
  @endphp

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

    {{-- ALERT --}}
    @if (session('ok'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        <ul class="list-disc ms-5">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    {{-- KARTU RINGKASAN --}}
    <div class="grid md:grid-cols-3 gap-4">
      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Status</div>
        <div class="mt-1">
          <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase border {{ $badge }}">
            {{ strtoupper($debtor->status) }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-5 text-sm">
          <div class="text-gray-500">Tenor</div>
          <div class="text-gray-900 font-semibold">{{ (int)$debtor->tenor }} bulan</div>

          <div class="text-gray-500">Angs ke (dibayar di muka)</div>
          <div class="text-gray-900 font-semibold">{{ (int)$debtor->installment_no }}</div>

          <div class="text-gray-500">Tgl Akad</div>
          <div class="text-gray-900 font-semibold">
            {{ $debtor->akad_date ? \Carbon\Carbon::parse($debtor->akad_date)->translatedFormat('d F Y') : '—' }}
          </div>

          <div class="text-gray-500">Angsuran / bln</div>
          <div class="text-gray-900 font-semibold">{{ $money($debtor->installment) }}</div>
        </div>
      </div>

      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Plafon</div>
        <div class="mt-1 text-2xl font-bold text-gray-900">{{ $money($debtor->plafond) }}</div>

        <div class="mt-5 text-sm text-gray-500">Outstanding (sisa kewajiban)</div>
        <div class="text-2xl font-bold text-gray-900">{{ $money($debtor->outstanding) }}</div>
      </div>

      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Arrears (tunggakan)</div>
        <div class="mt-1 text-2xl font-bold {{ (float)$debtor->arrears > 0 ? 'text-rose-700' : 'text-gray-900' }}">
          {{ $money($debtor->arrears) }}
        </div>

        <div class="mt-5 text-sm text-gray-500">Project</div>
        <div class="text-gray-900 font-semibold">
          {{ $debtor->project?->name ?? '—' }}
        </div>
      </div>
    </div>

    {{-- PAYMENT SCHEDULE --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Payment Schedule</h3>
          <p class="text-xs text-gray-500 mt-1">
            Baris berwarna kuning = angsuran yang sudah dibayar di muka (0).
          </p>
        </div>
        <div class="text-sm text-gray-500">
          Total periode: <span class="font-semibold text-gray-900">{{ $rows->count() }}</span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <div class="max-h-[560px] overflow-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 sticky top-0 z-10">
              <tr class="text-gray-900">
                <th class="px-4 py-3 text-left font-semibold">Angs Ke</th>
                <th class="px-4 py-3 text-left font-semibold">Tgl-Bln-Thn</th>
                <th class="px-4 py-3 text-right font-semibold">Out Standing</th>
                <th class="px-4 py-3 text-right font-semibold">Angs. Pokok</th>
                <th class="px-4 py-3 text-right font-semibold">Angs. Bunga</th>
                <th class="px-4 py-3 text-right font-semibold">Adm. Angsuran</th>
                <th class="px-4 py-3 text-right font-semibold">Total Angs.</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach ($rows as $r)
                @php
                  $d = \Carbon\Carbon::parse($r->period_date)->translatedFormat('d F Y');
                  $rowClass = $r->is_prepaid ? 'bg-amber-50' : 'hover:bg-gray-50';
                @endphp
                <tr class="{{ $rowClass }}">
                  <td class="px-4 py-3 text-gray-900">{{ $r->seq }}</td>
                  <td class="px-4 py-3 text-gray-900">{{ $d }}</td>
                  <td class="px-4 py-3 text-right text-gray-900">{{ $money($r->outstanding) }}</td>
                  <td class="px-4 py-3 text-right text-gray-900">{{ $money($r->pokok) }}</td>
                  <td class="px-4 py-3 text-right text-gray-900">{{ $money($r->bunga) }}</td>
                  <td class="px-4 py-3 text-right text-gray-900">{{ $money($r->adm) }}</td>
                  <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $money($r->total) }}</td>
                </tr>
              @endforeach
            </tbody>
            @php
              $sumPokok = $rows->sum('pokok');
              $sumBunga = $rows->sum('bunga');
              $sumAdm   = $rows->sum('adm');
              $sumTotal = $rows->sum('total');
            @endphp
            <tfoot class="bg-gray-50">
              <tr>
                <td colspan="2" class="px-4 py-3 text-right font-semibold text-gray-700">TOTAL</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">—</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $money($sumPokok) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $money($sumBunga) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $money($sumAdm) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $money($sumTotal) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div>
</x-app-layout>
