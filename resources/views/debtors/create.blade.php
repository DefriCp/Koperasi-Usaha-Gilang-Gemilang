<x-app-layout>
  <x-slot name="header">
    <div class="rounded-xl bg-indigo-700 text-white px-4 py-3">
      <h2 class="font-bold text-2xl">Tambah Debitur</h2>
    </div>
  </x-slot>

  @once
    <style>
      .u-input { height: 2.875rem; } /* h-11 */
      input[type=number].u-input::-webkit-outer-spin-button,
      input[type=number].u-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
      input[type=number].u-input { -moz-appearance: textfield; }
    </style>
  @endonce

  <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-slate-900 shadow-sm sm:rounded-2xl border border-slate-300 dark:border-slate-600">
      <div class="px-6 pt-6">
        @if ($errors->any())
          <div class="mb-4 text-white bg-rose-600 px-4 py-3 rounded-xl font-semibold">
            <ul class="list-disc ms-5">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

      <form method="POST" action="{{ route('debtors.store') }}" class="px-6 pb-8 text-slate-900 dark:text-slate-100">
        @csrf

        @php
          $label   = 'md:col-span-4 text-base md:text-lg font-bold';
          $field   = 'md:col-span-8';
          $input   = 'u-input w-full border border-slate-500 dark:border-slate-600 rounded-xl px-3.5 py-2.5 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder:text-slate-400 dark:placeholder:text-slate-300';
          $select  = $input;
          $textarea= 'w-full min-h-24 border border-slate-500 dark:border-slate-600 rounded-xl px-3.5 py-2.5 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder:text-slate-400 dark:placeholder:text-slate-300';
          $section = 'bg-gray-50 dark:bg-slate-800/70 px-4 py-2.5 text-base md:text-lg font-extrabold text-slate-900 dark:text-white';
          $box     = 'p-5 space-y-4';
        @endphp

        {{-- ===== A. INFORMASI PENGAJUAN ===== --}}
        <div class="border border-slate-300 dark:border-slate-600 rounded-2xl overflow-hidden">
          <div class="{{ $section }}">A. Informasi Pengajuan</div>
          <div class="{{ $box }}">
            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">1) Tanggal Input (TGL. Pengajuan)</label>
              <input type="date" name="input_date" value="{{ old('input_date', now()->format('Y-m-d')) }}" class="{{ $input }} {{ $field }}">
            </div>

            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">2) Nomor Pinjaman (No. Rekening Baru/Lama)</label>
              <input type="text" name="loan_number" value="{{ old('loan_number') }}" class="{{ $input }} {{ $field }}" placeholder="0 jika belum ada">
            </div>

            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">3) Project (PRODUK LOAN)</label>
              <div class="{{ $field }} grid md:grid-cols-2 gap-3">
                <input type="text" name="project_text" value="{{ old('project_text') }}" class="{{ $input }}" placeholder="Contoh: PLATINUM HASAMITRA">
                <select name="project_id" class="{{ $select }}">
                  <option value="">— pilih Project di DB (opsional) —</option>
                  @foreach ($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
              @error('project_id') <div class="md:col-start-5 md:col-span-8 text-rose-300 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">4) Jurabayar</label>
                <input type="text" name="payer" value="{{ old('payer') }}" class="{{ $input }} {{ $field }}" placeholder="PT. POS SURABAYA">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">5) Pensiun</label>
                <input type="text" name="pension" value="{{ old('pension') }}" class="{{ $input }} {{ $field }}" placeholder="Taspen / Asabri">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">6) Area</label>
                <input type="text" name="area" value="{{ old('area') }}" class="{{ $input }} {{ $field }}" placeholder="I / II / III / IV">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">7) Cabang</label>
                <input type="text" name="branch" value="{{ old('branch') }}" class="{{ $input }} {{ $field }}" placeholder="SIDOARJO 2">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center md:col-span-2">
                <label class="{{ $label }}">8) Pengajuan (Status Pembiayaan)</label>
                <input type="text" name="submission_type" value="{{ old('submission_type') }}" class="{{ $input }} {{ $field }}" placeholder="NEW / TAKE OVER / TOP UP">
              </div>
            </div>
          </div>
        </div>

        {{-- ===== B. DATA DEBITUR ===== --}}
        <div class="mt-6 border border-slate-300 dark:border-slate-600 rounded-2xl overflow-hidden">
          <div class="{{ $section }}">B. Data Debitur</div>
          <div class="{{ $box }}">
            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">9) Nama</label>
              <input name="name" value="{{ old('name') }}" class="{{ $input }} {{ $field }}" required>
              @error('name') <div class="md:col-start-5 md:col-span-8 text-rose-300 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="grid md:grid-cols-12 gap-3">
              <label class="{{ $label }} pt-2">10) Alamat (lengkap)</label>
              <div class="{{ $field }} space-y-3">
                <textarea name="address" rows="2" class="{{ $textarea }}" placeholder="Alamat jalan / RT RW">{{ old('address') }}</textarea>
                <div class="grid md:grid-cols-3 gap-3">
                  <input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="{{ $input }}" placeholder="Kelurahan">
                  <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="{{ $input }}" placeholder="Kecamatan">
                  <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" class="{{ $input }}" placeholder="Kota/Kab">
                </div>
                <div class="grid md:grid-cols-3 gap-3">
                  <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="{{ $input }}" placeholder="Provinsi">
                  <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" class="{{ $input }}" placeholder="Kode Pos">
                </div>
              </div>
            </div>

            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">11) Nopen (No. Pensiun)</label>
              <input name="nopen" value="{{ old('nopen') }}" class="{{ $input }} {{ $field }}" required>
              @error('nopen') <div class="md:col-start-5 md:col-span-8 text-rose-300 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">25) Angsuran Dimuka (Rp)</label>
                <input type="text" name="angsuran_dimuka" value="{{ old('angsuran_dimuka') }}" class="{{ $input }} {{ $field }}" placeholder="1495006">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">26) Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="{{ $input }} {{ $field }}">
              </div>
            </div>
          </div>
        </div>

        {{-- ===== C. KREDIT & BIAYA ===== --}}
        <div class="mt-6 border border-slate-300 dark:border-slate-600 rounded-2xl overflow-hidden">
          <div class="{{ $section }}">C. Kredit & Biaya</div>
          <div class="{{ $box }}">
            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">13) Plafond (Rp)</label>
              <input type="number" step="1" min="0" name="plafond" value="{{ old('plafond') }}" class="{{ $input }} {{ $field }}" required>
              @error('plafond') <div class="md:col-start-5 md:col-span-8 text-rose-300 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="grid md:grid-cols-3 gap-4">
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">19) Tenor (bulan)</label>
                <input type="number" min="1" name="tenor" value="{{ old('tenor') }}" class="{{ $input }}" required>
                @error('tenor') <div class="text-rose-300 text-sm mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">Angsuran ke-</label>
                <input type="number" min="0" name="installment_no" value="{{ old('installment_no',0) }}" class="{{ $input }}" required>
                @error('installment_no') <div class="text-rose-300 text-sm mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">27) Angsuran / bulan (Rp)</label>
                <input type="number" step="1" min="0" name="installment" value="{{ old('installment') }}" class="{{ $input }}" required>
                @error('installment') <div class="text-rose-300 text-sm mt-1">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">14) Bunga Bank (%)</label>
                <input type="text" name="interest_rate" value="{{ old('interest_rate') }}" class="{{ $input }} {{ $field }}" placeholder="14%">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">15) BAA (%)</label>
                <input type="text" name="baa_percent" value="{{ old('baa_percent') }}" class="{{ $input }} {{ $field }}">
              </div>

              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">16) Tgl Perjanjian Kredit (TGL.PK)</label>
                <input type="date" name="agreement_date" value="{{ old('agreement_date') }}" class="{{ $input }} {{ $field }}">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">17) Tgl Awal Kredit (TGL. Pembiayaan)</label>
                <input type="date" name="akad_date" value="{{ old('akad_date') }}" class="{{ $input }} {{ $field }}" required>
              </div>

              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">18) Tgl Akhir Kredit (Jatuh Tempo)</label>
                <input type="date" name="end_credit_date" value="{{ old('end_credit_date') }}" class="{{ $input }} {{ $field }}">
              </div>
              <div class="grid md:grid-cols-12 gap-3 items-center">
                <label class="{{ $label }}">20) Tgl Droping</label>
                <input type="date" name="disbursement_date" value="{{ old('disbursement_date') }}" class="{{ $input }} {{ $field }}">
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">21) Provisi (Rp)</label>
                <input type="text" name="provisi" value="{{ old('provisi') }}" class="{{ $input }}">
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">22) Administrasi (Rp)</label>
                <input type="text" name="administrasi" value="{{ old('administrasi') }}" class="{{ $input }}">
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">23) Asuransi (Rp)</label>
                <input type="text" name="asuransi" value="{{ old('asuransi') }}" class="{{ $input }}">
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">24) Tata Kelola (Rp)</label>
                <input type="text" name="tata_kelola" value="{{ old('tata_kelola') }}" class="{{ $input }}">
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">28) BAA (Rp)</label>
                <input type="text" name="baa_value" value="{{ old('baa_value') }}" class="{{ $input }}">
              </div>
              <div>
                <label class="block text-base md:text-lg font-bold mb-1.5">29) Total Angsuran (Rp)</label>
                <input type="text" name="total_installment" value="{{ old('total_installment') }}" class="{{ $input }}">
              </div>
            </div>
          </div>
        </div>

        {{-- ===== D. REKENING & BANK ===== --}}
        <div class="mt-6 border border-slate-300 dark:border-slate-600 rounded-2xl overflow-hidden">
          <div class="{{ $section }}">D. Rekening & Bank</div>
          <div class="{{ $box }}">
            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">30) Nomor Rekening</label>
              <input type="text" name="account_number" value="{{ old('account_number') }}" class="{{ $input }} {{ $field }}" placeholder="0 jika belum ada">
            </div>
            <div class="grid md:grid-cols-12 gap-3 items-center">
              <label class="{{ $label }}">31) Bank (KREDITUR / alias Project)</label>
              <input type="text" name="bank_alias" value="{{ old('bank_alias') }}" class="{{ $input }} {{ $field }}" placeholder="KB BANK / BPR HASAMITRA JABAR">
            </div>
          </div>
        </div>

        <div class="mt-6 flex items-center gap-3 px-1 pb-2">
          <button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold">Simpan</button>
          <a href="{{ route('debtors.index') }}" class="px-5 py-2.5 text-indigo-700 dark:text-indigo-300 border border-indigo-600 rounded-xl font-bold">Batal</a>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
