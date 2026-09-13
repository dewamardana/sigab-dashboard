<x-app-layout :title="'Monitoring Real-Time'">

  <div class="max-w-6xl mx-auto space-y-[21px] p-4 sm:ml-64 mt-14">

    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-lg font-semibold text-neutral-950">Monitoring Real-Time — Data Lengkap</h1>
        <p class="text-sm text-neutral-500">Termasuk sensor pendukung (baterai, suhu, dll) — hanya terlihat oleh admin &amp; superadmin.</p>
      </div>
      <span class="inline-flex items-center gap-1.5 text-[11px] font-mono font-medium text-primary-600 bg-primary-50 border border-primary-100 rounded-full px-3 py-1.5">
        <span class="relative flex h-1.5 w-1.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-primary-500"></span>
        </span>
        LIVE &middot; channel privat
      </span>
    </div>

    @forelse ($locations as $location)
      <div>
        <h2 class="text-sm font-semibold text-neutral-700 uppercase tracking-wider mb-3">
          {{ $location['name'] }} <span class="font-normal normal-case text-neutral-400">&middot; {{ $location['province'] }}</span>
        </h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @forelse ($location['devices'] as $d)
            <a href="{{ route('admin.monitoring.device', [$location['id'], $d['id']]) }}"
              data-device-id="{{ $d['device_id'] }}" data-recorded-at="{{ $d['recorded_at'] }}"
              class="block bg-white rounded-xl border border-neutral-200 p-4 hover:border-primary-300 hover:shadow-sm transition">
              <div class="flex items-start justify-between mb-3">
                <div class="min-w-0">
                  <p class="font-semibold text-neutral-950 text-sm truncate">{{ $d['name'] }}</p>
                  <p class="text-[11px] font-mono text-neutral-400 truncate">
                    {{ $d['device_id'] }} &middot; <span data-updated>{{ $d['recorded_at'] ? '' : '—' }}</span>
                  </p>
                </div>
                <x-status-badge :status="$d['status']" size="sm" class="shrink-0" />
              </div>

              {{-- Sensor "penentu status" (is_core) ditonjolkan dulu, baris sendiri --}}
              @php $coreReadings = $d['readings']->where('is_core', true); @endphp
              @if ($coreReadings->isNotEmpty())
                <div class="space-y-[8px] mb-3">
                  @foreach ($coreReadings as $r)
                    <div class="flex items-center justify-between text-[11px] text-neutral-500">
                      <span>{{ $r['name'] }}</span>
                      <span data-field="{{ $r['code'] }}" data-unit="{{ $r['unit'] }}" class="stat-mono font-semibold text-primary-700">
                        {{ $r['value'] ?? '-' }} {{ $r['unit'] }}
                      </span>
                    </div>
                  @endforeach
                </div>
              @endif

              {{-- Semua sensor lainnya - grid ringkas --}}
              <div class="grid grid-cols-2 gap-2 mb-3 pt-3 border-t border-neutral-100">
                @foreach ($d['readings']->where('is_core', false) as $r)
                  <div>
                    <p>
                      <span data-field="{{ $r['code'] }}" data-unit="{{ $r['unit'] }}" class="stat-mono text-[13px] font-semibold text-neutral-950">{{ $r['value'] ?? '-' }}</span>
                      <span class="text-[10px] text-neutral-400">{{ $r['unit'] }}</span>
                    </p>
                    <p class="text-[10px] text-neutral-500">{{ $r['name'] }}</p>
                  </div>
                @endforeach
              </div>

              <p class="text-[11px] text-primary-600 font-medium mt-3 pt-3 border-t border-neutral-100 inline-flex items-center gap-1">
                Lihat riwayat &amp; grafik
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
              </p>
            </a>
          @empty
            <p class="text-sm text-neutral-500 col-span-full">Belum ada perangkat di lokasi ini.</p>
          @endforelse
        </div>
      </div>
    @empty
      <p class="text-sm text-neutral-500 text-center py-12">
        Belum ada lokasi yang ditugaskan ke akun Anda.
      </p>
    @endforelse

  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-bar-gauge]').forEach((el) => {
          window.createBarGauge(el, {
            value: Number(el.dataset.value),
            siaga: Number(el.dataset.siaga),
            bahaya: Number(el.dataset.bahaya),
            max: Number(el.dataset.max),
            size: 'sm',
          });
        });

        document.querySelectorAll('[data-device-id]').forEach((card) => {
          const iso = card.dataset.recordedAt;
          const el = card.querySelector('[data-updated]');
          if (!el || !iso) return;
          el.textContent = window.timeAgo(iso);
          setInterval(() => { el.textContent = window.timeAgo(iso); }, 30000);
        });

        const locationIds = @json(collect($locations)->pluck('id'));

        locationIds.forEach((id) => {
          window.Echo.private(`admin.location.${id}`).listen('.sensor.updated', (data) => {
            const card = document.querySelector(`[data-device-id="${data.device_id}"]`);
            if (!card) return;

            window.applyStatusBadge(card.querySelector('.status-badge'), data.status);

            const updatedEl = card.querySelector('[data-updated]');
            if (updatedEl) updatedEl.textContent = window.timeAgo(data.recorded_at);

            const readings = data.readings || {};
            Object.keys(readings).forEach((code) => {
              const value = readings[code];
              if (value === null || value === undefined) return;

              const field = card.querySelector(`[data-field="${code}"]`);
              if (field) {
                const unit = field.dataset.unit || '';
                field.textContent = `${value} ${unit}`.trim();
              }

              const bar = card.querySelector(`[data-bar-gauge="${code}"]`);
              if (bar) {
                window.createBarGauge(bar, {
                  value, siaga: Number(bar.dataset.siaga), bahaya: Number(bar.dataset.bahaya),
                  max: Number(bar.dataset.max), size: 'sm',
                });
              }
            });
          });
        });
      });
    </script>
  @endpush

</x-app-layout>
