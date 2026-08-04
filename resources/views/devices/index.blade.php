<x-app-layout>

  <div class="max-w-5xl mx-auto space-y-6 p-4 sm:ml-64 mt-14" x-data="{ showAddModal: false, editingDevice: null }">

    @if (session('success'))
      <div class="p-4 rounded-xl bg-status-aman/10 text-status-aman text-sm font-medium">
        {{ session('success') }}
      </div>
    @endif

    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-neutral-950">Manajemen Perangkat</h2>
        <p class="text-sm text-neutral-600">Atur threshold status banjir per perangkat — Node-RED membaca ini
          otomatis.</p>
      </div>
      @if ($locations->count() > 0)
        <button @click="showAddModal = true"
          class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition shrink-0">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <span class="hidden sm:inline">Tambah Perangkat</span>
        </button>
      @endif
    </div>

    @if ($locations->count() === 0)
      <div class="p-4 rounded-xl bg-primary-50 text-primary-700 text-sm">
        Belum ada lokasi yang bisa dipilih. Tambahkan lokasi terlebih dahulu di menu Manajemen Lokasi.
      </div>
    @endif

    <div class="card rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-primary-50/60 text-primary-700 text-xs uppercase tracking-wider">
            <tr>
              <th class="px-4 py-3">Device ID</th>
              <th class="px-4 py-3 hidden sm:table-cell">Lokasi</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-primary-100">
            @forelse ($devices as $device)
              <tr class="hover:bg-primary-50/30">
                <td class="px-4 py-3">
                  <p class="font-medium text-neutral-950">{{ $device->device_id }}</p>
                  <p class="text-xs text-primary-500">{{ $device->name }}</p>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell text-primary-700">{{ $device->location->name }}</td>
                <td class="px-4 py-3">
                  @if ($device->is_active)
                    <span
                      class="text-xs px-2 py-1 rounded-full bg-status-aman/10 text-status-aman font-medium">Aktif</span>
                  @else
                    <span
                      class="text-xs px-2 py-1 rounded-full bg-primary-100 text-primary-500 font-medium">Nonaktif</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                  <button
                    @click="editingDevice = {{ \Illuminate\Support\Js::from(
                        $device->only([
                            'id',
                            'device_id',
                            'name',
                            'location_id',
                            'telegram_chat_id',
                            'is_active',
                        ]) + ['sensor_type_ids' => $device->sensorTypes->pluck('id')],
                    ) }}"
                    class="text-primary-600 hover:text-primary-800 text-xs font-medium">
                    Edit
                  </button>
                  <button x-data
                    @click="if (confirm('Hapus perangkat ini? Riwayat data sensor terkait TIDAK ikut terhapus.')) { $refs['delete-form-{{ $device->id }}'].submit() }"
                    class="text-status-bahaya hover:text-status-bahaya/70 text-xs font-medium">
                    Hapus
                  </button>
                  <form x-ref="delete-form-{{ $device->id }}" method="POST"
                    action="{{ route('devices.destroy', $device) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-primary-500 text-sm">
                  Belum ada perangkat terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Modal Tambah Perangkat --}}
    <div x-show="showAddModal" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40 overflow-y-auto py-8">
      <div @click.outside="showAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-auto">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Tambah Perangkat Baru</h3>
        <form method="POST" action="{{ route('devices.store') }}" class="space-y-4">
          @csrf
          @include('devices._form', ['device' => null])
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showAddModal = false"
              class="px-4 py-2 text-sm font-medium text-primary-700 hover:bg-primary-50 rounded-lg">Batal</button>
            <button type="submit"
              class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Edit Perangkat --}}
    <div x-show="editingDevice !== null" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40 overflow-y-auto py-8">
      <div @click.outside="editingDevice = null" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-auto"
        x-show="editingDevice">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Edit Perangkat</h3>
        <template x-if="editingDevice">
          <form method="POST" :action="'/devices/' + editingDevice.id" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Device ID</label>
              <input type="text" name="device_id" x-model="editingDevice.device_id" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama Perangkat</label>
              <input type="text" name="name" x-model="editingDevice.name"
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Lokasi</label>
              <select name="location_id" x-model="editingDevice.location_id" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
                @foreach ($locations as $loc)
                  <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Telegram Chat ID (opsional)</label>
              <input type="text" name="telegram_chat_id" x-model="editingDevice.telegram_chat_id"
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Sensor yang Dimiliki</label>
              <div class="grid grid-cols-2 gap-2">
                @foreach ($sensorTypes as $type)
                  <label class="flex items-center gap-2 text-sm text-primary-800">
                    <input type="checkbox" name="sensor_type_ids[]" value="{{ $type->id }}"
                      x-bind:checked="editingDevice.sensor_type_ids.includes({{ $type->id }})"
                      class="rounded border-primary-300 text-primary-600">
                    {{ $type->name }}
                  </label>
                @endforeach
              </div>
            </div>
            <label class="flex items-center gap-2">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" x-bind:checked="editingDevice.is_active"
                class="rounded border-primary-300 text-primary-600">
              <span class="text-sm text-primary-800">Perangkat aktif</span>
            </label>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="editingDevice = null"
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
