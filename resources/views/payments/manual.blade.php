<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Payment — Manual</h2>
  </x-slot>

  <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 mb-5">
      <form method="GET" action="{{ route('payments.manual') }}" class="grid gap-3 md:grid-cols-12">
        <div class="md:col-span-8">
          <label class="block text-sm font-semibold mb-1">NOPEN</label>
          <input type="text" name="nopen" value="{{ $nopen }}" class="h-11 w-full rounded-lg border px-3" placeholder="ketik NOPEN…">
        </div>
        <div class="md:col-span-4 flex items-end">
          <button class="h-11 px-4 rounded-lg border font-semibold bg-white hover:bg-gray-50">Tampilkan</button>
        </div>
      </form>
      @if(session('ok'))
        <div class="mt-3 rounded border border-emerald-300 bg-emerald-50 px-3 py-2 text-emerald-800">{{ session('ok') }}</div>
      @endif
    </div>

    @if($debtor)
      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
          <div class="text-gray-500">NOPEN</div>            <div class="font-semibold">{{ $debtor->nopen }}</div>
          <div class="text-gray-500">No Pinjaman</div>       <div class="font-semibold">{{ $summary->loan_number ?? '—' }}</div>
          <div class="text-gray-500">Plafond</div>           <div class="font-semibold">Rp {{ number_format($summary->plafond,0,',','.') }}</div>
          <div class="text-gray-500">Outstanding</div>       <div class="font-semibold">Rp {{ number_format($summary->outstanding,0,',','.') }}</div>
          <div class="text-gray-500">Bunga Bank (bulan berjalan)</div>
            <div class="font-semibold">Rp {{ number_format($summary->bunga_now,0,',','.') }}</div>
          <div class="text-gray-500">BAA (bulan berjalan)</div>
            <div class="font-semibold">Rp {{ number_format($summary->baa_now,0,',','.') }}</div>
          <div class="text-gray-500">Tunggakan</div>
            <div class="font-semibold">{{ $summary->arrears_months }} bln</div>
        </div>
      </div>

      <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-3 font-semibold border-b">Payment Schedule (terdekat)</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">Periode</th>
                <th class="px-4 py-2 text-right">Nominal</th>
                <th class="px-4 py-2 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($nexts as $r)
                <tr>
                  <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r->period_date)->translatedFormat('d F Y') }}</td>
                  <td class="px-4 py-2 text-right">Rp {{ number_format((float)$r->amount_due,0,',','.') }}</td>
                  <td class="px-4 py-2 text-center">{{ $r->status }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-600">Tidak ada angsuran tertunda.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <form method="POST" action="{{ route('payments.manual.pay') }}" class="flex items-end gap-3">
          @csrf
          <input type="hidden" name="nopen" value="{{ $debtor->nopen }}">
          <div>
            <label class="block text-sm font-semibold mb-1">Pembayaran (bulan)</label>
            <input type="number" name="months" value="1" min="1" class="h-11 w-40 rounded-lg border px-3">
          </div>
          <button class="h-11 px-4 rounded-lg border font-semibold bg-white hover:bg-gray-50">
            Proses Pembayaran
          </button>
        </form>
      </div>
    @endif
  </div>
</x-app-layout>
