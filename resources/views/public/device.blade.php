<x-public-layout :title="$device->name ?: $device->device_id">

  <section class="relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-200/50 rounded-full blur-3xl"></div>
    <div class="relative max-w-4xl mx-auto px-4 lg:px-6 pt-10 pb-6">
      <a href="{{ route('public.show', $location->id) }}"
        class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke {{ $location->name }}
      </a>
      <h1 class="text-2xl sm:text-3xl font-bold text-neutral-950 mt-3">{{ $device->name ?: $device->device_id }}</h1>
      <p class="text-sm text-neutral-600 font-mono">{{ $device->device_id }}</p>
    </div>
  </section>

  <main class="max-w-4xl mx-auto px-4 lg:px-6 pb-16 space-y-6">

    {{-- Status + Peta bento berdampingan --}}
    <div class="grid lg:grid-cols-2 gap-5">
      <div class="card p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
          <h2 class="text-sm font-semibold text-neutral-700">Status Saat Ini</h2>
          <span id="current-status"
            class="text-sm px-3 py-1 rounded-full font-semibold
                        {{ match ($latest['status'] ?? null) {
                            'BAHAYA' => 'bg-status-bahaya/10 text-status-bahaya',
                            'SIAGA' => 'bg-status-siaga/10 text-status-siaga',
                            'AMAN' => 'bg-status-aman/10 text-status-aman',
                            default => 'bg-neutral-100 text-neutral-500',
                        } }}">
            {{ $latest['status'] ?? 'Belum ada data' }}
          </span>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-xs text-neutral-500 mb-1.5">Tinggi Muka Air</p>
            <p id="current-tma" class="text-3xl font-bold text-neutral-950">{{ $latest['tma_cm'] ?? '-' }} <span
                class="text-sm font-normal text-neutral-500">cm</span></p>
          </div>
          <div>
            <p class="text-xs text-neutral-500 mb-1.5">Curah Hujan</p>
            <p id="current-hujan" class="text-3xl font-bold text-neutral-950">{{ $latest['hujan_mm'] ?? '-' }} <span
                class="text-sm font-normal text-neutral-500">mm</span></p>
          </div>
        </div>
      </div>

      <div class="card p-4 sm:p-5">
        <div id="map"
          class="rounded-2xl overflow-hidden w-full aspect-[4/5] sm:aspect-square lg:aspect-auto lg:h-full"
          style="min-height: 220px;"></div>
      </div>
    </div>

    {{-- Grafik per sensor — bento 2 kolom, dropdown filter tunggal --}}
    <div class="space-y-5" x-data="{ open: false, rangeDays: 7 }">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <h2 class="text-sm font-semibold text-neutral-700 uppercase tracking-wider">Grafik Historis</h2>

        <div class="relative">
          <button @click="open = !open"
            class="text-sm font-medium text-primary-600 inline-flex items-center gap-1.5 bg-white/80 backdrop-blur border border-white/60 rounded-full px-4 py-2 shadow-sm">
            <span x-text="rangeDays === 0 ? 'Semua data' : rangeDays + ' hari terakhir'"></span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
            </svg>
          </button>
          <div x-show="open" x-transition @click.outside="open = false" style="display:none"
            class="absolute right-0 z-20 mt-2 bg-white/95 backdrop-blur-xl border border-white/60 rounded-2xl shadow-lg w-44 overflow-hidden">
            <ul class="p-2 text-sm text-neutral-700 font-medium">
              @foreach ([1, 7, 30, 90, 0] as $r)
                <li>
                  <button type="button"
                    @click="rangeDays = {{ $r }}; window.updateAllDeviceCharts({{ $r }}); open = false"
                    class="w-full text-left px-3 py-2 hover:bg-primary-50 rounded-xl transition">
                    {{ $r === 0 ? 'Semua data' : $r . ' hari terakhir' }}
                  </button>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-5">
        @foreach ($sensorTypes as $type)
          <div class="card p-6">
            <h3 class="text-sm font-semibold text-neutral-950 mb-1">{{ $type->name }}</h3>
            <p class="text-xs text-neutral-500 mb-3">
              @if ($type->unit)
                Satuan: {{ $type->unit }}
              @endif
            </p>
            <div id="chart-{{ $type->code }}"></div>
          </div>
        @endforeach
      </div>
    </div>

  </main>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        // ===== Peta =====
        const map = L.map('map').setView([{{ $location->latitude }}, {{ $location->longitude }}], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $location->latitude }}, {{ $location->longitude }}]).addTo(map)
          .bindPopup('{{ $location->name }}');

        // ===== Data historis mentah dari server =====
        const history = @json($history);
        const sensorTypes = @json($sensorTypes);
        const charts = {};
        const rawPointsByCode = {};

        function filterPoints(points, days) {
          if (days === 0) return points;
          const cutoff = Date.now() - days * 24 * 60 * 60 * 1000;
          return points.filter(p => p[0] >= cutoff);
        }

        sensorTypes.forEach(type => {
          const points = history
            .map(h => {
              const value = type.code === 'tma_cm' ? h.tma_cm :
                type.code === 'hujan_mm' ? h.hujan_mm :
                (h.readings ? h.readings[type.code] : null);
              return value === null || value === undefined ? null : [new Date(h.recorded_at).getTime(), value];
            })
            .filter(p => p !== null);

          rawPointsByCode[type.code] = points;

          const chart = new ApexCharts(document.getElementById(`chart-${type.code}`), {
            ...window.softChartDefaults,
            chart: {
              ...window.softChartDefaults.chart,
              height: 200,
              type: 'line'
            },
            tooltip: {
              ...window.softChartDefaults.tooltip,
              x: {
                format: 'dd MMM HH:mm'
              }
            },
            series: [{
              name: type.name,
              data: filterPoints(points, 7),
              color: type.is_core ? '#2ba84a' : '#248232'
            }],
            legend: {
              show: true,
              position: 'top',
              horizontalAlign: 'right',
              fontSize: '12px'
            },
            xaxis: {
              ...window.softChartDefaults.xaxis,
              type: 'datetime'
            },
          });
          chart.render();
          charts[type.code] = chart;
        });

        window.updateAllDeviceCharts = function(days) {
          Object.keys(charts).forEach(code => {
            charts[code].updateSeries([{
              data: filterPoints(rawPointsByCode[code], days)
            }]);
          });
        };

        // ===== Real-time — hanya TMA & Hujan (lihat catatan payload Tahap 4) =====
        window.Echo.channel('location.{{ $location->id }}').listen('.sensor.updated', (data) => {
          if (data.device_id !== '{{ $device->device_id }}') return;

          document.getElementById('current-status').textContent = data.status;
          document.getElementById('current-tma').innerHTML = data.tma_cm +
            ' <span class="text-sm font-normal text-neutral-500">cm</span>';
          document.getElementById('current-hujan').innerHTML = data.hujan_mm +
            ' <span class="text-sm font-normal text-neutral-500">mm</span>';

          const t = new Date(data.recorded_at).getTime();
          if (rawPointsByCode['tma_cm']) rawPointsByCode['tma_cm'].push([t, data.tma_cm]);
          if (rawPointsByCode['hujan_mm']) rawPointsByCode['hujan_mm'].push([t, data.hujan_mm]);

          const alpineRoot = document.querySelector('[x-data*="rangeDays"]');
          const activeDays = alpineRoot ? alpineRoot.__x.$data.rangeDays : 7;
          window.updateAllDeviceCharts(activeDays);
        });
      });
    </script>
  @endpush

</x-public-layout>
