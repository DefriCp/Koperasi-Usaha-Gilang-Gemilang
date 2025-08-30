<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
      {{ $p->exists ? 'Edit Data Pensiun' : 'Tambah Data Pensiun' }}
    </h2>
  </x-slot>

  <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
    <form method="post" action="{{ $p->exists ? route('pensions.update',$p) : route('pensions.store') }}"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-6">
      @csrf
      @if($p->exists) @method('PUT') @endif

      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">NIP</label>
          <input name="nip" value="{{ old('nip',$p->nip) }}" class="w-full h-11 rounded-lg border-gray-300" required>
          @error('nip')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NAMA_PENSIUNAN</label>
          <input name="name" value="{{ old('name',$p->name) }}" class="w-full h-11 rounded-lg border-gray-300" required>
          @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NO_KTP</label>
          <input name="ktp" value="{{ old('ktp',$p->ktp) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">ALAMAT</label>
          <input name="address_line1" value="{{ old('address_line1',$p->address_line1) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NAMA_DATI4</label>
          <input name="address_line2" value="{{ old('address_line2',$p->address_line2) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NAMA_DATI2</label>
          <input name="address_line3" value="{{ old('address_line3',$p->address_line3) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">TELEPON</label>
          <input name="phone" value="{{ old('phone',$p->phone) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NO_HP</label>
          <input name="phone_alt" value="{{ old('phone_alt',$p->phone_alt) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">NPWP</label>
          <input name="npwp" value="{{ old('npwp',$p->npwp) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">TGL_LAHIR_PENSIUNAN</label>
          <input type="date" name="birth_date" value="{{ old('birth_date',$p->birth_date?->format('Y-m-d')) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <div><label class="text-sm font-semibold text-gray-700">KODE_CABANG</label>
          <input name="branch_code" value="{{ old('branch_code',$p->branch_code) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">NAMA_CABANG</label>
          <input name="branch_name" value="{{ old('branch_name',$p->branch_name) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">KODE_JENIS_PENSIUN</label>
          <input name="jenis_pensiun_code" value="{{ old('jenis_pensiun_code',$p->jenis_pensiun_code) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">NAMA_JENIS_PENSIUN</label>
          <input name="jenis_pensiun_name" value="{{ old('jenis_pensiun_name',$p->jenis_pensiun_name) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <div><label class="text-sm font-semibold text-gray-700">KODE_JIWA</label>
          <input name="kode_jiwa" value="{{ old('kode_jiwa',$p->kode_jiwa) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">NOMOR_SKEP</label>
          <input name="nomor_skep" value="{{ old('nomor_skep',$p->nomor_skep) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">TMT_PENSIUN</label>
          <input type="date" name="tmt_pensiun" value="{{ old('tmt_pensiun',$p->tmt_pensiun?->format('Y-m-d')) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">TANGGAL_SKEP</label>
          <input type="date" name="tanggal_skep" value="{{ old('tanggal_skep',$p->tanggal_skep?->format('Y-m-d')) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div><label class="text-sm font-semibold text-gray-700">KODE_JURU_BAYAR</label>
          <input name="payer_code" value="{{ old('payer_code',$p->payer_code) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">NAMA_JURU_BAYAR</label>
          <input name="payer_name" value="{{ old('payer_name',$p->payer_name) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
        <div><label class="text-sm font-semibold text-gray-700">NO_REKENING</label>
          <input name="account_number" value="{{ old('account_number',$p->account_number) }}" class="w-full h-11 rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        @php $fields=['penpok'=>'PENPOK','tunj_istri'=>'TUNJANGAN_ISTRI','tunj_anak'=>'TUNJANGAN_ANAK','tunj_beras'=>'TUNJANGAN_BERAS']; @endphp
        @foreach($fields as $key=>$label)
          <div>
            <label class="text-sm font-semibold text-gray-700">{{ $label }}</label>
            <input name="{{ $key }}" value="{{ old($key,$p->$key) }}" class="w-full h-11 rounded-lg border-gray-300">
          </div>
        @endforeach
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        @php $fields=['penyesuaian'=>'PENYESUAIAN','tunj_bulat'=>'TUNJANGAN_BULAT','total_kotor'=>'TOTAL_KOTOR','bersih'=>'BERSIH']; @endphp
        @foreach($fields as $key=>$label)
          <div>
            <label class="text-sm font-semibold text-gray-700">{{ $label }}</label>
            <input name="{{ $key }}" value="{{ old($key,$p->$key) }}" class="w-full h-11 rounded-lg border-gray-300">
          </div>
        @endforeach
      </div>

      <div class="flex gap-3">
        <button class="h-11 px-5 rounded-lg border bg-white">Simpan</button>
        <a href="{{ $p->exists ? route('pensions.index') : route('pensions.index') }}" class="h-11 px-5 rounded-lg border bg-white">Batal</a>
      </div>
    </form>
  </div>
</x-app-layout>
