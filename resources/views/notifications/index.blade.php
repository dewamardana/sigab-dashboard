<x-app-layout :title="'Log Notifikasi'">

  <div class="max-w-6xl mx-auto space-y-[21px] p-4 sm:ml-64 mt-14">

    <div>
      <h1 class="text-lg font-semibold text-neutral-950">Log Notifikasi</h1>
      <p class="text-sm text-neutral-500">Riwayat setiap alert Telegram yang dikirim — termasuk yang gagal terkirim.</p>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-neutral-200 p-4 grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <select name="location_id" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua lokasi</option>
        @foreach ($locations as $loc)
          <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name }}</option>
        @endforeach
      </select>

      <select name="status" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua status</option>
        @foreach (['AMAN', 'SIAGA', 'BAHAYA'] as $s)
          <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
        @endforeach
      </select>

      <select name="is_sent" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua hasil kirim</option>
        <option value="1" @selected(request('is_sent') === '1')>Berhasil terkirim</option>
        <option value="0" @selected(request('is_sent') === '0')>Gagal terkirim</option>
      </select>

      <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-neutral-200 text-sm" placeholder="Dari tanggal">
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-neutral-200 text-sm" placeholder="Sampai tanggal">

      <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
        <button type="submit" class="btn-primary !px-4 !py-2 text-sm">Terapkan Filter</button>
        <a href="{{ route('notifications.index') }}" class="text-sm text-neutral-500 hover:text-primary-600 px-2 py-2">Reset</a>
      </div>
    </form>

    <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="text-left px-4 py-3">Waktu</th>
            <th class="text-left px-4 py-3">Lokasi</th>
            <th class="text-left px-4 py-3">Perangkat</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Hasil Kirim</th>
            <th class="text-left px-4 py-3">Pesan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
          @forelse ($logs as $log)
            <tr class="hover:bg-neutral-50">
              <td class="px-4 py-3 font-mono text-xs text-neutral-500 whitespace-nowrap">
                {{ $log->sent_at?->format('d M Y, H:i') ?? '-' }}
              </td>
              <td class="px-4 py-3">{{ $log->device->location->name ?? '-' }}</td>
              <td class="px-4 py-3">{{ $log->device->name ?: $log->device->device_id ?? '-' }}</td>
              <td class="px-4 py-3"><x-status-badge :status="$log->status" size="sm" /></td>
              <td class="px-4 py-3">
                @if ($log->is_sent)
                  <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-primary-50 text-primary-700">Terkirim</span>
                @else
                  <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-status-bahaya/10 text-status-bahaya">Gagal</span>
                @endif
              </td>
              <td class="px-4 py-3 text-neutral-600 max-w-xs truncate" title="{{ $log->message }}">{{ $log->message ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-neutral-500">Belum ada log notifikasi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $logs->links() }}

  </div>

</x-app-layout>
