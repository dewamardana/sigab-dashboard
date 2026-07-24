<x-app-layout>

  <div class="max-w-5xl mx-auto space-y-6 p-4 sm:ml-64 mt-14" x-data="{ showAddModal: false, editingLocation: null }">

    {{-- Notifikasi sukses --}}
    @if (session('success'))
      <div class="p-4 rounded-xl bg-status-aman/10 text-status-aman text-sm font-medium">
        {{ session('success') }}
      </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-neutral-950">Manajemen Lokasi</h2>
        <p class="text-sm text-neutral-600">Kelola titik pemantauan banjir — tambah lokasi baru tanpa perlu ubah
          Node-RED.</p>
      </div>
      <button @click="showAddModal = true"
        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        <span class="hidden sm:inline">Tambah Lokasi</span>
      </button>
    </div>

    {{-- Tabel — responsive: scroll horizontal di mobile --}}
    <div class="card rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-primary-50/60 text-primary-700 text-xs uppercase tracking-wider">
            <tr>
              <th class="px-4 py-3">Nama Lokasi</th>
              <th class="px-4 py-3 hidden sm:table-cell">Provinsi</th>
              <th class="px-4 py-3 hidden md:table-cell">Koordinat</th>
              <th class="px-4 py-3">Perangkat</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-primary-100">
            @forelse ($locations as $location)
              <tr class="hover:bg-primary-50/30">
                <td class="px-4 py-3">
                  <p class="font-medium text-neutral-950">{{ $location->name }}</p>
                  <p class="text-xs text-primary-500 sm:hidden">{{ $location->province }}</p>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell text-primary-700">{{ $location->province ?? '-' }}</td>
                <td class="px-4 py-3 hidden md:table-cell text-primary-700 text-xs">
                  {{ $location->latitude }}, {{ $location->longitude }}
                </td>
                <td class="px-4 py-3 text-primary-700">{{ $location->devices_count }}</td>
                <td class="px-4 py-3">
                  @if ($location->is_active)
                    <span
                      class="text-xs px-2 py-1 rounded-full bg-status-aman/10 text-status-aman font-medium">Aktif</span>
                  @else
                    <span
                      class="text-xs px-2 py-1 rounded-full bg-primary-100 text-primary-500 font-medium">Nonaktif</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                  <button
                    @click="editingLocation = {{ \Illuminate\Support\Js::from($location->only(['id', 'name', 'description', 'latitude', 'longitude', 'province', 'is_active'])) }}"
                    class="text-primary-600 hover:text-primary-800 text-xs font-medium">
                    Edit
                  </button>
                  <button x-data
                    @click="if (confirm('Hapus lokasi ini? Semua perangkat di lokasi ini akan ikut terhapus.')) { $refs['delete-form-{{ $location->id }}'].submit() }"
                    class="text-status-bahaya hover:text-status-bahaya/70 text-xs font-medium">
                    Hapus
                  </button>
                  <form x-ref="delete-form-{{ $location->id }}" method="POST"
                    action="{{ route('locations.destroy', $location) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-primary-500 text-sm">
                  Belum ada lokasi terdaftar. Klik "Tambah Lokasi" untuk mulai.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Modal Tambah Lokasi --}}
    <div x-show="showAddModal" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40">
      <div @click.outside="showAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Tambah Lokasi Baru</h3>
        <form method="POST" action="{{ route('locations.store') }}" class="space-y-4">
          @csrf
          @include('locations.form')
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showAddModal = false"
              class="px-4 py-2 text-sm font-medium text-primary-700 hover:bg-primary-50 rounded-lg">Batal</button>
            <button type="submit"
              class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Edit Lokasi --}}
    <div x-show="editingLocation !== null" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40">
      <div @click.outside="editingLocation = null" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6"
        x-show="editingLocation">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Edit Lokasi</h3>
        <template x-if="editingLocation">
          <form method="POST" :action="'/locations/' + editingLocation.id" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama Lokasi</label>
              <input type="text" name="name" x-model="editingLocation.name" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Provinsi</label>
              <input type="text" name="province" x-model="editingLocation.province"
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block mb-1.5 text-sm font-medium text-neutral-950">Latitude</label>
                <input type="number" step="0.0000001" name="latitude" x-model="editingLocation.latitude" required
                  class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              </div>
              <div>
                <label class="block mb-1.5 text-sm font-medium text-neutral-950">Longitude</label>
                <input type="number" step="0.0000001" name="longitude" x-model="editingLocation.longitude" required
                  class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              </div>
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Deskripsi</label>
              <textarea name="description" x-model="editingLocation.description" rows="2"
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30"></textarea>
            </div>
            <label class="flex items-center gap-2">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" x-bind:checked="editingLocation.is_active"
                class="rounded border-primary-300 text-primary-600">
              <span class="text-sm text-primary-800">Lokasi aktif</span>
            </label>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="editingLocation = null"
                class="px-4 py-2 text-sm font-medium text-primary-700 hover:bg-primary-50 rounded-lg">Batal</button>
              <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan
                Perubahan</button>
            </div>
          </form>
        </template>
      </div>
    </div>

  </div>

</x-app-layout>
