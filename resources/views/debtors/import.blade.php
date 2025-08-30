<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-2xl text-black-900 leading-tight">Import Debitur</h2>
      <a href="{{ route('debtors.index') }}"
         class="inline-flex items-center h-10 px-4 rounded-lg border border-gray-300 text-black-900 font-semibold bg-white hover:bg-gray-50">
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
      <form method="POST" action="{{ route('debtors.import.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-semibold text-gray-800 mb-1">File Excel (.xls/.xlsx)</label>
          <input type="file" name="file" accept=".xls,.xlsx,.xlsm"
                 class="block w-full rounded-lg border border-gray-300 px-3 py-2">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-800 mb-1">Default Project (opsional)</label>
          <select name="project_id" class="h-11 w-full rounded-lg border border-gray-300 px-3">
            <option value="">— Jangan paksa, coba deteksi dari kolom —</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-gray-500 mt-2">
            Catatan: proses import <strong>tidak akan membuat Project baru</strong>. Jika nama bank di Excel tidak cocok
            dengan Project yang sudah ada, barisnya akan dilewati. Pilih “Default Project” bila ingin memaksa semua baris
            memakai project tertentu.
          </p>
        </div>

        <div class="pt-2">
          <button class="h-11 px-6 rounded-lg border border-blue-600 text-blue-700 font-semibold bg-white hover:bg-blue-50">
            Mulai Import
          </button>
        </div>

        <div class="pt-4 text-sm text-gray-600">
          Minimal header yang dikenali: <code class="font-mono">NO PENSIUN / NOPEN</code> dan <code class="font-mono">NAMA / NAMA DEBITUR</code>.
          Kolom lain yang membantu: <code class="font-mono">TENOR</code>, <code class="font-mono">ANGSURAN PER BULAN</code>,
          <code class="font-mono">PLAFOND</code>, <code class="font-mono">TGL.PK / TGL PEMBIAYAAN</code>,
          serta salah satu kolom proyek seperti <code class="font-mono">KREDITUR / PRODUK LOAN / BANK</code>.
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
