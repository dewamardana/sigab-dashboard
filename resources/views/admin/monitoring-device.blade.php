<x-app-layout :title="$device->name ?: $device->device_id">
  @php
    $displaySensors = $sensorTypes; // sudah diurutkan is_core dulu dari controller
  @endphp

  <div class="max-w-6xl mx-auto space-y-[21px] p-4 sm:ml-64 mt-14">

    <a href="{{ route('admin.monitoring') }}" class="text-[13px] text-primary-600 inline-flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Monitoring Real-Time
    </a>

    <div class="flex flex-wrap items-center justify-between gap-[13px]">
      <div>
        <h1 class="text-[22px] font-bold text-neutral-950 leading-tight">{{ $device->name ?: $device->device_id }}</h1>
        <p class="text-[12px] font-mono text-neutral-400">
          {{ $device->device_id }} &middot; {{ $location->name }}, {{ $location->province }} &middot; diperbarui
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

    {{-- ===================== RIWAYAT SENSOR — KARTU TERPISAH ===================== --}}
    <div>
      <h2 class="text-[16px] font-semibold text-neutral-950 mb-[13px]">Riwayat Sensor</h2>
      <div class="grid sm:grid-cols-2 gap-[13px]">
        @foreach ($sensorTypes as $type)
          <div class="bg-white rounded-2xl border border-neutral-200 p-[21px] {{ $type->is_core ? 'ring-1 ring-primary-200' : '' }}">
            <div class="flex items-center gap-2 mb-[13px]">
              <h3 class="text-[14px] font-semibold text-neutral-950">{{ $type->name }}</h3>
              @if ($type->is_core)
                <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded">Penentu Status</span>
              @endif
              @unless ($type->is_public)
                <span class="text-[10px] font-semibold uppercase tracking-wide text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">Privat</span>
              @endunless
              @if ($type->unit)
                <span class="text-[11px] text-neutral-400 ml-auto">{{ $type->unit }}</span>
              @endif
            </div>
            <div data-sensor-chart="{{ $type->code }}"></div>
          </div>
        @endforeach
      </div>
    </div>

  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        
        const recordedAt = @json($latest['recorded_at'] ?? null);
        const updatedEl = document.getElementById('updated-ago');
        
        if (updatedEl && recordedAt) {
          updatedEl.textContent = window.timeAgo(recordedAt);
          setInterval(() => { updatedEl.textContent = window.timeAgo(recordedAt); }, 30000);
        }

        const sensorCharts = window.renderSensorCharts(document, {
          sensorTypes: @json($sensorTypes),
          history: @json($history),
        });

        // Channel PRIVAT — data lengkap termasuk baterai.
        window.Echo.private('admin.location.{{ $location->id }}').listen('.sensor.updated', (data) => {
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

</x-app-layout>
