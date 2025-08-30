<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit Project</h2></x-slot>
  <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
      <form method="post" action="{{ route('projects.update',$project) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm mb-1">Nama Project</label>
          <input name="name" value="{{ old('name',$project->name) }}" class="border rounded px-3 py-2 w-full">
          @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div class="flex items-center gap-2">
          <input id="is_active" type="checkbox" name="is_active" value="1" {{ $project->is_active ? 'checked' : '' }}>
          <label for="is_active">Aktif</label>
        </div>
        <div class="flex gap-2">
          <a href="{{ route('projects.index') }}" class="px-4 py-2 rounded border">Batal</a>
          <button class="px-4 py-2 rounded bg-indigo-600 text-white">Update</button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
