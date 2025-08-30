<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Edit Debitur</h2>
      <a href="{{ route('debtors.show',$debtor) }}"
         class="inline-flex items-center h-10 px-4 rounded-lg border border-gray-300 text-gray-900 font-semibold bg-white hover:bg-gray-50">
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
    @if (session('ok'))
      <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-rose-800">
        <ul class="list-disc ms-5">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('debtors.update',$debtor) }}" class="space-y-6">
      @csrf @method('PUT')

      <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 space-y-5">
        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Project</label>
            <select name="project_id" class="h-11 w-full rounded-lg border border-gray-300 px-3">
              <option value="">— Pilih Project —</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(old('project_id',$debtor->project_id)==$p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Nopen</label>
            <input type="text" name="nopen" value="{{ old('nopen',$debtor->nopen) }}"
                   class="h-11 w-full rounded-lg border border-gray-300 px-3" required>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Nama Penerima</label>
            <input type="text" name="name" value="{{ old('name',$debtor->name) }}"
                   class="h-11 w-full rounded-lg border border-gray-300 px-3" required>
          </div>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Plafond</label>
            <input type="text" name="plafond" value="{{ old('plafond',$debtor->plafond) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Angsuran/bln</label>
            <input type="text" name="installment" value="{{ old('installment',$debtor->installment) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Tenor (bulan)</label>
            <input type="number" name="tenor" value="{{ old('tenor',$debtor->tenor) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Angsuran ke</label>
            <input type="number" name="installment_no" value="{{ old('installment_no',$debtor->installment_no) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Tgl Akad</label>
            <input type="date" name="akad_date" value="{{ old('akad_date',optional($debtor->akad_date)->format('Y-m-d')) }}"
                   class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Alias Bank (opsional)</label>
            <input type="text" name="bank_alias" value="{{ old('bank_alias',$detail?->bank_alias) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">No. Pinjaman / Kode Mitra</label>
            <input type="text" name="loan_number" value="{{ old('loan_number',$detail?->loan_number) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">No. Rekening</label>
            <input type="text" name="account_number" value="{{ old('account_number',$detail?->account_number) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Bunga (%)</label>
            <input type="text" name="interest_rate" value="{{ old('interest_rate',$detail?->interest_rate) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Adm. Angsuran</label>
            <input type="text" name="administrasi" value="{{ old('administrasi',$detail?->administrasi) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Tgl Mulai Kredit</label>
            <input type="date" name="start_credit_date" value="{{ old('start_credit_date',$detail?->start_credit_date) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Tgl Pencairan</label>
            <input type="date" name="disbursement_date" value="{{ old('disbursement_date',$detail?->disbursement_date) }}" class="h-11 w-full rounded-lg border border-gray-300 px-3">
          </div>
        </div>
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button class="h-11 px-6 rounded-lg border border-blue-600 text-blue-700 font-semibold bg-white hover:bg-blue-50">
          Simpan Perubahan
        </button>

        @role('inputer|checker')
          <form method="POST" action="{{ route('debtors.destroy',$debtor) }}"
                onsubmit="return confirm('Hapus debitur ini? Data repayment juga akan dihapus.');">
            @csrf @method('DELETE')
            <button type="submit" class="h-11 px-6 rounded-lg border border-rose-600 text-rose-700 font-semibold bg-white hover:bg-rose-50">
              Hapus
            </button>
          </form>
        @endrole
      </div>
    </form>
  </div>
</x-app-layout>
