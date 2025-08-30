<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900">Import Data Pensiun</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('pensions.import.store') }}" enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6">
      @csrf
      <div class="mb-3">
        <label class="font-semibold">File Excel (xls/xlsx/csv)</label>
        <input type="file" name="file" class="mt-1 block w-full">
      </div>
      <button class="px-5 h-11 rounded-lg bg-indigo-600 text-white font-semibold">Upload & Import</button>
    </form>

    <div class="mt-6 text-sm text-gray-600">
      Kolom minimal yang dikenali: <code>NIP</code>, <code>NAMA_PENSIUNAN</code>.
      Kolom lain seperti <code>NO_KTP</code>, <code>KODE_CABANG</code>, <code>NAMA_CABANG</code>,
      <code>PENPOK</code>, <code>BERSIH</code>, dsb akan dibaca otomatis jika ada.
    </div>
  </div>
</x-app-layout>
