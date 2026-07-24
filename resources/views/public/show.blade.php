<x-public-layout :title="$location->name">

  <section class="relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-200/50 rounded-full blur-3xl"></div>
    <div class="relative max-w-5xl mx-auto px-4 lg:px-6 pt-10 pb-6">
      <a href="{{ route('public.index') }}#peta"
        class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Peta
      </a>
      <h1 class="text-2xl sm:text-3xl font-bold text-neutral-950 mt-3">{{ $location->name }}</h1>
      <p class="text-sm text-neutral-600">{{ $location->province }} &bull; {{ $deviceCards->count() }} perangkat
        terpasang</p>
    </div>
  </section>

  <main class="max-w-5xl mx-auto px-4 lg:px-6 pb-16 space-y-10">

    {{-- Daftar device — bento card --}}
    <div>
      <h2 class="text-sm font-semibold text-neutral-700 uppercase tracking-wider mb-4">Perangkat di Lokasi Ini</h2>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($deviceCards as $d)
          <a href="{{ route('public.device', [$location->id, $d['id']]) }}"
            class="group card p-6 hover:shadow-[0_16px_40px_rgb(4,15,15,0.08)] hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-start justify-between mb-3">
              <p class="font-semibold text-neutral-950 group-hover:text-primary-700 transition">{{ $d['name'] }}</p>
              <span
                class="text-xs px-2 py-1 rounded-full font-medium shrink-0
                            {{ match ($d['latest']['status'] ?? null) {
                                'BAHAYA' => 'bg-status-bahaya/10 text-status-bahaya',
                                'SIAGA' => 'bg-status-siaga/10 text-status-siaga',
                                'AMAN' => 'bg-status-aman/10 text-status-aman',
                                default => 'bg-neutral-100 text-neutral-500',
                            } }}">
                {{ $d['latest']['status'] ?? 'Belum ada data' }}
              </span>
            </div>
            <p class="text-xs text-neutral-400 font-mono mb-4">{{ $d['device_id'] }}</p>
            @if ($d['latest'])
              <div class="text-xs text-neutral-600 grid grid-cols-2 gap-2 pt-3 border-t border-neutral-100">
                <div>TMA: <span class="font-medium text-neutral-950">{{ $d['latest']['tma_cm'] }} cm</span></div>
                <div>Hujan: <span class="font-medium text-neutral-950">{{ $d['latest']['hujan_mm'] }} mm</span></div>
              </div>
            @endif
          </a>
        @empty
          <p class="text-sm text-neutral-500 col-span-full text-center py-8">Belum ada perangkat terdaftar.</p>
        @endforelse
      </div>
    </div>

    {{-- Grafik gabungan — bento 2 kolom, dropdown filter tunggal --}}
    <div class="space-y-5" x-data="{ open: false, rangeDays: 7 }">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <h2 class="text-sm font-semibold text-neutral-700 uppercase tracking-wider">Grafik Gabungan Semua Perangkat</h2>

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
                    @click="rangeDays = {{ $r }}; window.updateAllCombinedCharts({{ $r }}); open = false"
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
        @forelse ($charts as $chartData)
          <div class="card p-6">
            <h3 class="text-sm font-semibold text-neutral-950 mb-1">{{ $chartData['name'] }}</h3>
            <p class="text-xs text-neutral-500 mb-3">Perbandingan antar perangkat @if ($chartData['unit'])
                (satuan: {{ $chartData['unit'] }})
              @endif
            </p>
            <div id="combined-chart-{{ $chartData['code'] }}"></div>
          </div>
        @empty
          <div class="card p-10 text-center text-neutral-500 text-sm sm:col-span-2">
            Belum ada data sensor untuk ditampilkan.
          </div>
        @endforelse
      </div>
    </div>

  </main>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const charts = @json($charts);
        const renderedCharts = {};

        function filterPoints(points, days) {
          if (days === 0) return points;
          const cutoff = Date.now() - days * 24 * 60 * 60 * 1000;
          return points.filter(p => p[0] >= cutoff);
        }

        charts.forEach(chartData => {
          const rawSeries = chartData.series.map(s => ({
            name: s.name,
            data: s.data.map(p => [p.x, p.y]),
          }));

          const seriesIndexByDeviceId = {};
          chartData.series.forEach((s, i) => {
            seriesIndexByDeviceId[s.device_id] = i;
          });

          const initialSeries = rawSeries.map(s => ({
            name: s.name,
            data: filterPoints(s.data, 7)
          }));

          const chart = new ApexCharts(document.getElementById(`combined-chart-${chartData.code}`), {
            ...window.softChartDefaults,
            chart: {
              ...window.softChartDefaults.chart,
              height: 220,
              type: 'line'
            },
            tooltip: {
              ...window.softChartDefaults.tooltip,
              shared: false,
              x: {
                format: 'dd MMM HH:mm'
              }
            },
            series: initialSeries,
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
          renderedCharts[chartData.code] = {
            chart,
            rawSeries,
            seriesIndexByDeviceId
          };
        });

        window.updateAllCombinedCharts = function(days) {
          Object.values(renderedCharts).forEach(({
            chart,
            rawSeries
          }) => {
            const filtered = rawSeries.map(s => ({
              name: s.name,
              data: filterPoints(s.data, days)
            }));
            chart.updateSeries(filtered);
          });
        };

        window.Echo.channel('location.{{ $location->id }}').listen('.sensor.updated', (data) => {
          ['tma_cm', 'hujan_mm'].forEach(code => {
            const entry = renderedCharts[code];
            if (!entry) return;
            const idx = entry.seriesIndexByDeviceId[data.device_id];
            if (idx === undefined) return;

            const value = code === 'tma_cm' ? data.tma_cm : data.hujan_mm;
            entry.rawSeries[idx].data.push([new Date(data.recorded_at).getTime(), value]);

            const alpineRoot = document.querySelector('[x-data*="rangeDays"]');
            const activeDays = alpineRoot ? alpineRoot.__x.$data.rangeDays : 7;
            window.updateAllCombinedCharts(activeDays);
          });
        });
      });
    </script>
  @endpush

</x-public-layout>
