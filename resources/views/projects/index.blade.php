<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Project</h2>
  </x-slot>

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      @if (session('ok'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-3 py-2 rounded">
          {{ session('ok') }}
        </div>
      @endif

      <div class="flex items-center justify-between mb-4">
        <form method="get">
          <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Cari nama..." />
        </form>

        @role('checker')
          <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Tambah Project</a>
        @endrole
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2">Nama Project</th>
              <th class="py-2">Aktif</th>
              @role('checker')<th class="py-2">Aksi</th>@endrole
            </tr>
          </thead>
          <tbody>
            @forelse($projects as $p)
              <tr class="border-b">
                <td class="py-2">{{ $p->name }}</td>
                <td class="py-2">
                  <span class="px-2 py-1 rounded text-xs {{ $p->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                    {{ $p->is_active ? 'AKTIF' : 'NONAKTIF' }}
                  </span>
                </td>
                @role('checker')
                <td class="py-2 space-x-2">
                  <a href="{{ route('projects.edit',$p) }}" class="text-indigo-600">Edit</a>
                  <form action="{{ route('projects.destroy',$p) }}" method="post" class="inline" onsubmit="return confirm('Hapus project?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600">Hapus</button>
                  </form>
                </td>
                @endrole
              </tr>
            @empty
              <tr><td class="py-3 text-gray-500" colspan="3">Belum ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $projects->withQueryString()->links() }}</div>
    </div>
  </div>
</x-app-layout>
