<x-app-layout :title="$device->name ?: $device->device_id">

  @php
    $latestFull = $history->last();
    $secondaryTypes = $sensorTypes->where('is_core', false);

    $tmaValue = $latest['tma_cm'] ?? null;
    $tmaStatus = is_null($tmaValue)
        ? null
        : ($tmaValue < $device->threshold_tma_siaga ? 'AMAN' : ($tmaValue < $device->threshold_tma_bahaya ? 'SIAGA' : 'BAHAYA'));
    $tmaGradient = match ($tmaStatus) {
        'BAHAYA' => 'linear-gradient(135deg, #e02424, #7a1616)',
        'SIAGA' => 'linear-gradient(135deg, #e3a008, #7a5b04)',
        default => 'linear-gradient(135deg, #2ba84a, #15421c)',
    };

    $hujanValue = $latest['hujan_mm'] ?? null;
    $hujanStatus = is_null($hujanValue)
        ? null
        : ($hujanValue < $device->threshold_hujan_siaga ? 'AMAN' : ($hujanValue < $device->threshold_hujan_bahaya ? 'SIAGA' : 'BAHAYA'));
    $hujanBorderColor = match ($hujanStatus) {
        'BAHAYA' => '#e02424',
        'SIAGA' => '#e3a008',
        default => '#2ba84a',
    };

    $iconPaths = [
        'suhu' => '<rect x="10" y="3" width="4" height="12" rx="2"/><circle cx="12" cy="18" r="3"/>',
        'kelembapan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3c3 4 6 7.5 6 11a6 6 0 1 1-12 0c0-3.5 3-7 6-11Z"/>',
        'angin_kmph' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8h11a3 3 0 1 0-3-3"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h15a3 3 0 1 1-3 3"/>',
        'baterai_v' => '<rect x="2" y="7" width="18" height="10" rx="2.5"/><rect x="21" y="10" width="2" height="4" rx="1" fill="currentColor" stroke="none"/>',
    ];
    $fallbackIcon = '<circle cx="12" cy="12" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2"/>';
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

    {{-- ===================== TMA & HUJAN — SETARA ===================== --}}
    <div class="grid md:grid-cols-2 gap-[13px]">

      <div id="tma-hero" class="relative overflow-hidden rounded-2xl p-[21px] text-white" style="background: {{ $tmaGradient }};">
        <svg class="absolute inset-0 w-full h-full opacity-[0.14]" viewBox="0 0 400 220" preserveAspectRatio="none" fill="none">
          <path d="M0 80 C60 50,100 50,160 80 S260 110,320 80 S400 50,400 80" stroke="white" stroke-width="3" />
          <path d="M0 140 C60 110,100 110,160 140 S260 170,320 140 S400 110,400 140" stroke="white" stroke-width="3" />
        </svg>
        <div class="relative flex items-center justify-between mb-[13px]">
          <p class="text-[12px] uppercase tracking-widest text-primary-100/80">Tinggi muka air</p>
          <span id="tma-zone" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white/15"></span>
        </div>
        <div class="relative flex items-end justify-between gap-[13px]">
          <p class="flex items-baseline gap-1.5">
            <span id="tma-value" class="stat-mono text-[40px] font-bold leading-none">{{ $latest['tma_cm'] ?? '-' }}</span>
            <span class="text-[14px] text-primary-100/80">cm</span>
          </p>
          <div class="w-[120px] shrink-0" id="tma-gauge"></div>
        </div>
        <p class="relative text-[11px] text-primary-100/70 mt-[8px]">
          Ambang siaga {{ $device->threshold_tma_siaga }}cm &middot; bahaya {{ $device->threshold_tma_bahaya }}cm
        </p>
      </div>

      <div id="hujan-card" class="bg-white rounded-2xl border border-neutral-200 p-[21px] border-t-4" style="border-top-color:{{ $hujanBorderColor }}">
        <div class="flex items-center justify-between mb-[13px]">
          <p class="text-[12px] uppercase tracking-widest text-neutral-500">Curah hujan</p>
          <span id="hujan-zone" class="text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
        </div>
        <p class="flex items-baseline gap-1.5 mb-[21px]">
          <span id="hujan-value" class="stat-mono text-[40px] font-bold leading-none text-neutral-950">{{ $latest['hujan_mm'] ?? '-' }}</span>
          <span class="text-[14px] text-neutral-400">mm</span>
        </p>
        <div id="hujan-bar" class="mb-[8px]"></div>
        <p class="text-[11px] text-neutral-400">
          Ambang siaga {{ $device->threshold_hujan_siaga }}mm &middot; bahaya {{ $device->threshold_hujan_bahaya }}mm
        </p>
      </div>
    </div>

    {{-- ===================== SENSOR PENDUKUNG (termasuk baterai) ===================== --}}
    @if ($secondaryTypes->isNotEmpty())
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-[13px]">
        @foreach ($secondaryTypes as $type)
          <div class="bg-white rounded-xl border border-neutral-200 p-[13px] {{ !$type->is_public ? 'ring-1 ring-amber-200' : '' }}">
            <div class="flex items-center justify-between mb-[8px]">
              <div class="w-7 h-7 rounded-lg bg-primary-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  {!! $iconPaths[$type->code] ?? $fallbackIcon !!}
                </svg>
              </div>
              @unless ($type->is_public)
                <span class="text-[9px] font-semibold uppercase text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">Privat</span>
              @endunless
            </div>
            <p data-field="{{ $type->code }}" data-unit="{{ $type->unit }}" class="stat-mono text-[20px] font-bold text-neutral-950 leading-tight">
              {{ $latestFull?->getReading($type->code) ?? '-' }}<span class="text-[11px] font-sans font-normal text-neutral-400"> {{ $type->unit }}</span>
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
        const DEVICE = {
          tma: {{ $latest['tma_cm'] ?? 0 }},
          hujan: {{ $latest['hujan_mm'] ?? 0 }},
          tmaSiaga: {{ $device->threshold_tma_siaga }},
          tmaBahaya: {{ $device->threshold_tma_bahaya }},
          tmaMax: {{ $tmaGaugeMax }},
          hujanSiaga: {{ $device->threshold_hujan_siaga }},
          hujanBahaya: {{ $device->threshold_hujan_bahaya }},
          hujanMax: {{ $hujanGaugeMax }},
        };
        const recordedAt = @json($latest['recorded_at'] ?? null);

        const HERO_GRADIENTS = {
          AMAN: 'linear-gradient(135deg, #2ba84a, #15421c)',
          SIAGA: 'linear-gradient(135deg, #e3a008, #7a5b04)',
          BAHAYA: 'linear-gradient(135deg, #e02424, #7a1616)',
        };

        function paintZone(elId, value, siaga, bahaya, plain) {
          const status = window.classifyValue(value, siaga, bahaya);
          const style = window.statusStyle(status);
          const el = document.getElementById(elId);
          if (!el) return;
          if (plain) {
            el.className = `text-[11px] font-bold px-2 py-0.5 rounded-full ${style.bg} ${style.text}`;
          }
          el.textContent = style.label.toUpperCase();
        }
        paintZone('tma-zone', DEVICE.tma, DEVICE.tmaSiaga, DEVICE.tmaBahaya, false);
        paintZone('hujan-zone', DEVICE.hujan, DEVICE.hujanSiaga, DEVICE.hujanBahaya, true);

        const gauge = window.createTmaGauge(document.getElementById('tma-gauge'), {
          value: DEVICE.tma, siaga: DEVICE.tmaSiaga, bahaya: DEVICE.tmaBahaya, max: DEVICE.tmaMax, variant: 'hero',
        });
        window.createBarGauge(document.getElementById('hujan-bar'), {
          value: DEVICE.hujan, siaga: DEVICE.hujanSiaga, bahaya: DEVICE.hujanBahaya, max: DEVICE.hujanMax,
        });

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
          document.getElementById('tma-value').textContent = data.tma_cm;
          document.getElementById('hujan-value').textContent = data.hujan_mm;

          paintZone('tma-zone', data.tma_cm, DEVICE.tmaSiaga, DEVICE.tmaBahaya, false);
          paintZone('hujan-zone', data.hujan_mm, DEVICE.hujanSiaga, DEVICE.hujanBahaya, true);

          const newTmaStatus = window.classifyValue(data.tma_cm, DEVICE.tmaSiaga, DEVICE.tmaBahaya);
          document.getElementById('tma-hero').style.background = HERO_GRADIENTS[newTmaStatus] || HERO_GRADIENTS.AMAN;

          const newHujanStatus = window.classifyValue(data.hujan_mm, DEVICE.hujanSiaga, DEVICE.hujanBahaya);
          document.getElementById('hujan-card').style.borderTopColor = window.statusStyle(newHujanStatus).hex;

          gauge.update(data.tma_cm);
          window.createBarGauge(document.getElementById('hujan-bar'), {
            value: data.hujan_mm, siaga: DEVICE.hujanSiaga, bahaya: DEVICE.hujanBahaya, max: DEVICE.hujanMax,
          });

          if (updatedEl) updatedEl.textContent = window.timeAgo(data.recorded_at);

          const t = new Date(data.recorded_at).getTime();
          sensorCharts.pushPoint('tma_cm', t, data.tma_cm);
          sensorCharts.pushPoint('hujan_mm', t, data.hujan_mm);

          const readings = data.readings || {};
          Object.keys(readings).forEach((code) => {
            if (code === 'tma_cm' || code === 'hujan_mm') return;
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
