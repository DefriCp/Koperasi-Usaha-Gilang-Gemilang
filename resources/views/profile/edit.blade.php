<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Profil
    </h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
        <div>
          <h3 class="font-semibold text-lg mb-2">Data Akun</h3>
          <p>Nama: {{ $user->name }}</p>
          <p>Email: {{ $user->email }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
          @csrf
          @method('PATCH')

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full border rounded-md p-2"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full border rounded-md p-2"/>
          </div>

          <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Simpan</button>
        </form>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin hapus akun?');">
          @csrf
          @method('DELETE')
          <button class="px-4 py-2 bg-red-600 text-white rounded-md">Hapus Akun</button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
