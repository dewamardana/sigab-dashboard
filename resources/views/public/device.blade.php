<x-public-layout :title="$device->name ?: $device->device_id">
  @php
    // Ikon per kode sensor - dipakai grid "SENSOR" di bawah. Sempat
    // terhapus tidak sengaja bareng blok hero TMA/Hujan lama (penyebab
    // error "Undefined variable $fallbackIcon"); ditambahkan lagi di
    // sini + ikon baru utk sensor hasil revisi fuzzy on-device.
    $iconPaths = [
        'suhu' => '<rect x="10" y="3" width="4" height="12" rx="2"/><circle cx="12" cy="18" r="3"/>',
        'kelembapan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3c3 4 6 7.5 6 11a6 6 0 1 1-12 0c0-3.5 3-7 6-11Z"/>',
        'angin_kmph' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8h11a3 3 0 1 0-3-3"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h15a3 3 0 1 1-3 3"/>',
        'baterai_v' => '<rect x="2" y="7" width="18" height="10" rx="2.5"/><rect x="21" y="10" width="2" height="4" rx="1" fill="currentColor" stroke="none"/>',
        'tma_cm' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 9c2-1.5 4-1.5 6 0s4 1.5 6 0 4-1.5 6 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 15c2-1.5 4-1.5 6 0s4 1.5 6 0 4-1.5 6 0"/>',
        'hujan_mm' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 14a4 4 0 0 1 .5-7.97A5.5 5.5 0 0 1 17 8a3.5 3.5 0 0 1-1 6.9H6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l-1 2M13 18l-1 2M17 18l-1 2"/>',
        'hujan_intensitas' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 14a4 4 0 0 1 .5-7.97A5.5 5.5 0 0 1 17 8a3.5 3.5 0 0 1-1 6.9H6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l-1 2M13 18l-1 2M17 18l-1 2"/>',
        'hujan_kategori' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 16a4 4 0 0 1 .5-7.97A5.5 5.5 0 0 1 17 9a3.5 3.5 0 0 1-1 6.9H6Z"/>',
        'freeboard_m' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M9 6l3-3 3 3M9 18l3 3 3-3"/>',
        'status_skor' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 15a8 8 0 1 1 16 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15l3.5-4.5"/><circle cx="12" cy="15" r="1" fill="currentColor" stroke="none"/>',
        'level_kritis' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4 3 19h18L12 4Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v4"/><circle cx="12" cy="17" r="0.7" fill="currentColor" stroke="none"/>',
    ];
    $fallbackIcon = '<circle cx="12" cy="12" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2"/>';
  @endphp

  <main class="max-w-6xl mx-auto px-4 lg:px-6 py-[21px] space-y-[21px]">

    <a href="{{ route('public.show', $location->id) }}" class="text-[13px] text-primary-600 inline-flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      {{ $location->name }}
    </a>

    <div class="flex flex-wrap items-center justify-between gap-[13px]">
      <div>
        <h1 class="text-[26px] font-bold text-neutral-950 leading-tight">{{ $device->name ?: $device->device_id }}</h1>
        <p class="text-[12px] font-mono text-neutral-400">
          {{ $device->device_id }} &middot; diperbarui
          <span id="updated-ago">{{ isset($latest['recorded_at']) ? '' : '—' }}</span>
        </p>
      </div>
      <x-status-badge id="status-pill" :status="$latest['status'] ?? null" size="lg" />
    </div>

    {{-- ===================== SENSOR — SEMUA SETARA, PENENTU STATUS DITANDAI ===================== --}}
    @if ($sensorTypes->isNotEmpty())
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-[13px]">
        @foreach ($sensorTypes as $type)
          <div class="bg-white rounded-xl border border-neutral-200 p-[13px] {{ $type->is_core ? 'ring-1 ring-primary-200' : '' }} {{ !$type->is_public ? 'ring-1 ring-amber-200' : '' }}">
            <div class="flex items-center justify-between mb-[8px]">
              <div class="w-7 h-7 rounded-lg bg-primary-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  {!! $iconPaths[$type->code] ?? $fallbackIcon !!}
                </svg>
              </div>
              @if ($type->is_core)
                <span class="text-[9px] font-semibold uppercase text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded">Penentu Status</span>
              @endif
            </div>
            @php($val = $latestFull?->getReading($type->code))
            <p data-field="{{ $type->code }}" data-unit="{{ $type->unit }}" class="stat-mono text-[20px] font-bold text-neutral-950 leading-tight">
              {{ $val ?? '-' }}<span class="text-[11px] font-sans font-normal text-neutral-400"> {{ $type->unit }}</span>
            </p>
            <p class="text-[11px] text-neutral-500">{{ $type->name }}</p>
          </div>
        @endforeach
      </div>
    @endif

    <a href="{{ route('public.show', $location->id) }}#peta-lokasi"
      class="flex items-center gap-[13px] group bg-white rounded-2xl border border-neutral-200 p-[13px]">
      <div id="minimap" class="w-[100px] h-[100px] sm:w-[130px] sm:h-[130px] rounded-xl overflow-hidden shrink-0 ring-1 ring-neutral-200"></div>
      <div>
        <p class="text-[13px] font-medium text-neutral-950 group-hover:text-primary-600 transition">{{ $location->name }}</p>
        <p class="text-[12px] text-neutral-500">{{ $location->province }} &middot; lihat lokasi di peta</p>
      </div>
    </a>

    {{-- ===================== RIWAYAT SENSOR — KARTU TERPISAH, TANPA TAB ===================== --}}
    <div>
      <h2 class="text-[16px] font-semibold text-neutral-950 mb-[13px]">Riwayat Sensor</h2>
      <div class="grid sm:grid-cols-2 gap-[13px]">
        @foreach ($sensorTypes as $type)
          <div class="bg-white rounded-2xl border border-neutral-200 p-[21px] {{ $type->is_core ? 'ring-1 ring-primary-200' : '' }}">
            <div class="flex items-center gap-2 mb-[13px]">
              <h3 class="text-[14px] font-semibold text-neutral-950">{{ $type->name }}</h3>
              @if ($type->is_core)
                <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded">
                  Penentu Status
                </span>
              @endif
              @if ($type->unit)
                <span class="text-[11px] text-neutral-400 ml-auto">{{ $type->unit }}</span>
              @endif
            </div>
            <div data-sensor-chart="{{ $type->code }}"></div>
          </div>
        @endforeach
      </div>
    </div>

  </main>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const recordedAt = @json($latest['recorded_at'] ?? null);

        const updatedEl = document.getElementById('updated-ago');
        if (updatedEl && recordedAt) {
          updatedEl.textContent = window.timeAgo(recordedAt);
          setInterval(() => { updatedEl.textContent = window.timeAgo(recordedAt); }, 30000);
        }

        const map = L.map('minimap', { zoomControl: false, dragging: false, scrollWheelZoom: false, attributionControl: false })
          .setView([{{ $location->latitude }}, {{ $location->longitude }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.circleMarker([{{ $location->latitude }}, {{ $location->longitude }}], {
          radius: 6, fillColor: window.statusStyle('{{ $latest['status'] ?? "AMAN" }}').hex, color: '#fff', weight: 2, fillOpacity: 1
        }).addTo(map);

        const sensorCharts = window.renderSensorCharts(document, {
          sensorTypes: @json($sensorTypes),
          history: @json($history),
        });

        window.Echo.channel('location.{{ $location->id }}').listen('.sensor.updated', (data) => {
          if (data.device_id !== '{{ $device->device_id }}') return;

          window.applyStatusBadge(document.getElementById('status-pill'), data.status);
          if (updatedEl) updatedEl.textContent = window.timeAgo(data.recorded_at);

          const t = new Date(data.recorded_at).getTime();
          const readings = data.readings || {};
          Object.keys(readings).forEach((code) => {
            const value = readings[code];
            const field = document.querySelector(`[data-field="${code}"]`);
            if (field && value !== null && value !== undefined) {
              const unit = field.dataset.unit || '';
              field.innerHTML = `${value}<span class="text-[11px] font-sans font-normal text-neutral-400"> ${unit}</span>`;
            }
            sensorCharts.pushPoint(code, t, value);
          });
        });
      });
    </script>
  @endpush

</x-public-layout>
