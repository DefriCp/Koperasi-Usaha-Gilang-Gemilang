<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
      Data Pensiun — {{ $p->name }} ({{ $p->nip }})
    </h2>
  </x-slot>

  <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <div class="grid md:grid-cols-3 gap-x-8 gap-y-3 text-sm">
        <div class="font-semibold text-gray-600">NOMOR PENSIUN</div><div class="col-span-2">: {{ $p->nip }}</div>
        <div class="font-semibold text-gray-600">NAMA</div><div class="col-span-2">: {{ $p->name }}</div>

        <div class="font-semibold text-gray-600">ALAMAT</div>
        <div class="col-span-2">:
          {{ $p->address_line1 }}<br>
          {{ $p->address_line2 }}<br>
          {{ $p->address_line3 }}
        </div>

        <div class="font-semibold text-gray-600">NO TELEPON</div><div class="col-span-2">: {{ $p->phone ?: '-' }}</div>
        <div class="font-semibold text-gray-600">NO KTP</div><div class="col-span-2">: {{ $p->ktp ?: '-' }}</div>
        <div class="font-semibold text-gray-600">TGL LAHIR</div><div class="col-span-2">: {{ optional($p->birth_date)->format('d M Y') ?: '-' }}</div>
        <div class="font-semibold text-gray-600">NO NPWP</div><div class="col-span-2">: {{ $p->npwp ?: '-' }}</div>

        <div class="font-semibold text-gray-600">CABANG TASPEN PENSIUN</div><div class="col-span-2">: {{ $p->branch_code }} — {{ $p->branch_name }}</div>
        <div class="font-semibold text-gray-600">KODE JENIS PENSIUN</div><div class="col-span-2">: {{ $p->jenis_pensiun_code }} — {{ $p->jenis_pensiun_name }}</div>
        <div class="font-semibold text-gray-600">KODE JIWA</div><div class="col-span-2">: {{ $p->kode_jiwa ?: '-' }}</div>
        <div class="font-semibold text-gray-600">NOMOR SKEP</div><div class="col-span-2">: {{ $p->nomor_skep ?: '-' }}</div>
        <div class="font-semibold text-gray-600">TMT PENSIUN</div><div class="col-span-2">: {{ optional($p->tmt_pensiun)->format('d M Y') ?: '-' }}</div>

        <div class="font-semibold text-gray-600">NAMA JURU BAYAR</div><div class="col-span-2">: {{ $p->payer_name ?: '-' }}</div>
        <div class="font-semibold text-gray-600">NOMOR REKENING</div><div class="col-span-2">: {{ $p->account_number ?: '-' }}</div>

        <div class="font-semibold text-gray-600">PENSIUN POKOK</div><div class="col-span-2">: Rp {{ number_format($p->penpok,0,',','.') }}</div>
        <div class="font-semibold text-gray-600">TUNJ ISTRI</div><div class="col-span-2">: Rp {{ number_format($p->tunj_istri,0,',','.') }}</div>
        <div class="font-semibold text-gray-600">TUNJ ANAK</div><div class="col-span-2">: Rp {{ number_format($p->tunj_anak,0,',','.') }}</div>
        <div class="font-semibold text-gray-600">TUNJ BERAS</div><div class="col-span-2">: Rp {{ number_format($p->tunj_beras,0,',','.') }}</div>
        <div class="font-semibold text-gray-600">TOTAL KOTOR</div><div class="col-span-2">: Rp {{ number_format($p->total_kotor,0,',','.') }}</div>
        <div class="font-semibold text-gray-600">BERSIH</div><div class="col-span-2">: <b>Rp {{ number_format($p->bersih,0,',','.') }}</b></div>
      </div>

      <div class="mt-6 flex gap-3">
        <a class="h-10 px-4 rounded-lg border bg-white" href="{{ route('pensions.edit',$p) }}">Edit</a>
        <form method="post" action="{{ route('pensions.destroy',$p) }}"
              onsubmit="return confirm('Hapus data ini?')">
          @csrf @method('DELETE')
          <button class="h-10 px-4 rounded-lg border bg-white text-red-700">Hapus</button>
        </form>
        <a class="h-10 px-4 rounded-lg border bg-white" href="{{ route('pensions.index') }}">Kembali</a>
      </div>
    </div>

  </div>
</x-app-layout>
