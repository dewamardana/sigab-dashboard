<x-app-layout :title="'Riwayat & Laporan'">

  <div class="max-w-6xl mx-auto space-y-[21px] p-4 sm:ml-64 mt-14">

    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-lg font-semibold text-neutral-950">Riwayat &amp; Laporan</h1>
        <p class="text-sm text-neutral-500">Data sensor historis mentah, per baris — untuk pelaporan/audit yang butuh angka presisi.</p>
      </div>
      <a href="{{ route('reports.export', request()->query()) }}" class="btn-primary !px-4 !py-2 text-sm inline-flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12" /><path stroke-linecap="round" stroke-linejoin="round" d="m7 10 5 5 5-5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14" />
        </svg>
        Unduh CSV
      </a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-neutral-200 p-4 grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <select name="location_id" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua lokasi</option>
        @foreach ($locations as $loc)
          <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name }}</option>
        @endforeach
      </select>

      <select name="device_id" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua perangkat</option>
        @foreach ($locations->flatMap->devices as $d)
          <option value="{{ $d->id }}" @selected(request('device_id') == $d->id)>{{ $d->name ?: $d->device_id }}</option>
        @endforeach
      </select>

      <select name="status" class="rounded-lg border-neutral-200 text-sm">
        <option value="">Semua status</option>
        @foreach (['AMAN', 'SIAGA', 'BAHAYA'] as $s)
          <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
        @endforeach
      </select>

      <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-neutral-200 text-sm">
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-neutral-200 text-sm">

      <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
        <button type="submit" class="btn-primary !px-4 !py-2 text-sm">Terapkan Filter</button>
        <a href="{{ route('reports.index') }}" class="text-sm text-neutral-500 hover:text-primary-600 px-2 py-2">Reset</a>
      </div>
    </form>

    <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="text-left px-4 py-3">Waktu</th>
            <th class="text-left px-4 py-3">Lokasi</th>
            <th class="text-left px-4 py-3">Perangkat</th>
            <th class="text-right px-4 py-3">TMA</th>
            <th class="text-right px-4 py-3">Hujan</th>
            <th class="text-left px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
          @forelse ($records as $r)
            <tr class="hover:bg-neutral-50">
              <td class="px-4 py-3 font-mono text-xs text-neutral-500 whitespace-nowrap">
                {{ $r->recorded_at?->format('d M Y, H:i') ?? '-' }}
              </td>
              <td class="px-4 py-3">{{ $r->device->location->name ?? '-' }}</td>
              <td class="px-4 py-3">{{ $r->device->name ?: $r->device->device_id ?? '-' }}</td>
              <td class="px-4 py-3 text-right stat-mono">{{ $r->tma_cm ?? '-' }} <span class="text-neutral-400">cm</span></td>
              <td class="px-4 py-3 text-right stat-mono">{{ $r->hujan_mm ?? '-' }} <span class="text-neutral-400">mm</span></td>
              <td class="px-4 py-3"><x-status-badge :status="$r->status" size="sm" /></td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-neutral-500">Belum ada data sensor untuk filter ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $records->links() }}

  </div>

</x-app-layout>
