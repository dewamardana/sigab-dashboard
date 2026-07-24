<x-app-layout>

  <div class="max-w-5xl mx-auto space-y-6 p-4 sm:ml-64 mt-14" x-data="{ showAddModal: false, editingUser: null }">

    @if (session('success'))
      <div class="p-4 rounded-xl bg-status-aman/10 text-status-aman text-sm font-medium">
        {{ session('success') }}
      </div>
    @endif

    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-neutral-950">Manajemen User</h2>
        <p class="text-sm text-neutral-600">Kelola akun pengelola sistem dan penugasan lokasinya.</p>
      </div>
      <button @click="showAddModal = true"
        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        <span class="hidden sm:inline">Tambah User</span>
      </button>
    </div>

    <div class="card rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-primary-50/60 text-primary-700 text-xs uppercase tracking-wider">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3 hidden sm:table-cell">Email</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3 hidden md:table-cell">Lokasi Ditugaskan</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-primary-100">
            @forelse ($users as $user)
              <tr class="hover:bg-primary-50/30">
                <td class="px-4 py-3">
                  <p class="font-medium text-neutral-950">{{ $user->name }}</p>
                  <p class="text-xs text-primary-500 sm:hidden">{{ $user->email }}</p>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell text-primary-700">{{ $user->email }}</td>
                <td class="px-4 py-3">
                  <span class="text-xs px-2 py-1 rounded-full bg-primary-100 text-primary-700 font-medium">
                    {{ $user->roles->first()?->name ?? '-' }}
                  </span>
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-primary-700 text-xs">
                  {{ $user->locations->pluck('name')->join(', ') ?: '-' }}
                </td>
                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                  <button
                    @click="editingUser = {{ \Illuminate\Support\Js::from([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->roles->first()?->name,
                        'location_ids' => $user->locations->pluck('id'),
                    ]) }}"
                    class="text-primary-600 hover:text-primary-800 text-xs font-medium">
                    Edit
                  </button>
                  @if ($user->id !== auth()->id())
                    <button x-data
                      @click="if (confirm('Hapus user ini?')) { $refs['delete-form-{{ $user->id }}'].submit() }"
                      class="text-status-bahaya hover:text-status-bahaya/70 text-xs font-medium">
                      Hapus
                    </button>
                    <form x-ref="delete-form-{{ $user->id }}" method="POST"
                      action="{{ route('users.destroy', $user) }}" class="hidden">
                      @csrf
                      @method('DELETE')
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-8 text-center text-primary-500 text-sm">Belum ada user lain.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Modal Tambah User --}}
    <div x-show="showAddModal" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40 overflow-y-auto py-8">
      <div @click.outside="showAddModal = false" x-data="{ role: 'admin_lokasi' }"
        class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-auto">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Tambah User Baru</h3>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required
              class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
          </div>
          <div>
            <label class="block mb-1.5 text-sm font-medium text-neutral-950">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
              class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Password</label>
              <input type="password" name="password" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Konfirmasi</label>
              <input type="password" name="password_confirmation" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
          </div>
          <div>
            <label class="block mb-1.5 text-sm font-medium text-neutral-950">Role</label>
            <select name="role" x-model="role" required
              class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              <option value="admin_lokasi">Admin Lokasi</option>
              <option value="superadmin">Superadmin</option>
            </select>
          </div>
          <div x-show="role === 'admin_lokasi'">
            <label class="block mb-1.5 text-sm font-medium text-neutral-950">Lokasi yang Ditugaskan</label>
            <div class="space-y-1 max-h-32 overflow-y-auto border border-neutral-200rounded-lg p-2">
              @foreach ($locations as $loc)
                <label class="flex items-center gap-2 text-sm text-primary-800">
                  <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                    class="rounded border-primary-300 text-primary-600">
                  {{ $loc->name }}
                </label>
              @endforeach
            </div>
            <x-input-error :messages="$errors->get('location_ids')" class="mt-1.5" />
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showAddModal = false"
              class="px-4 py-2 text-sm font-medium text-primary-700 hover:bg-primary-50 rounded-lg">Batal</button>
            <button type="submit"
              class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Edit User --}}
    <div x-show="editingUser !== null" x-transition style="display: none;"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/40 overflow-y-auto py-8">
      <div @click.outside="editingUser = null" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-auto"
        x-show="editingUser">
        <h3 class="text-base font-semibold text-neutral-950 mb-4">Edit User</h3>
        <template x-if="editingUser">
          <form method="POST" :action="'/users/' + editingUser.id" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama</label>
              <input type="text" name="name" x-model="editingUser.name" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Email</label>
              <input type="email" name="email" x-model="editingUser.email" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block mb-1.5 text-sm font-medium text-neutral-950">Password Baru (opsional)</label>
                <input type="password" name="password"
                  class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              </div>
              <div>
                <label class="block mb-1.5 text-sm font-medium text-neutral-950">Konfirmasi</label>
                <input type="password" name="password_confirmation"
                  class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
              </div>
            </div>
            <div>
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Role</label>
              <select name="role" x-model="editingUser.role" required
                class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
                <option value="admin_lokasi">Admin Lokasi</option>
                <option value="superadmin">Superadmin</option>
              </select>
            </div>
            <div x-show="editingUser.role === 'admin_lokasi'">
              <label class="block mb-1.5 text-sm font-medium text-neutral-950">Lokasi yang Ditugaskan</label>
              <div class="space-y-1 max-h-32 overflow-y-auto border border-neutral-200rounded-lg p-2">
                @foreach ($locations as $loc)
                  <label class="flex items-center gap-2 text-sm text-primary-800">
                    <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                      x-bind:checked="editingUser.location_ids.includes({{ $loc->id }})"
                      class="rounded border-primary-300 text-primary-600">
                    {{ $loc->name }}
                  </label>
                @endforeach
              </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="editingUser = null"
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
