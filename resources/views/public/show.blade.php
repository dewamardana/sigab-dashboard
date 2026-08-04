<x-public-layout :title="$location->name">

  @php
    $overall = $statusCounts['BAHAYA'] > 0 ? 'BAHAYA' : ($statusCounts['SIAGA'] > 0 ? 'SIAGA' : 'AMAN');
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

    <a href="{{ route('public.index') }}#peta" class="text-[13px] text-primary-600 inline-flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Semua lokasi
    </a>

    {{-- ===================== HERO LOKASI ===================== --}}
    <div class="relative overflow-hidden rounded-2xl p-[21px] text-white" style="background: linear-gradient(135deg, #2ba84a, #15421c);">
      <svg class="absolute inset-0 w-full h-full opacity-[0.12]" viewBox="0 0 800 200" preserveAspectRatio="none" fill="none">
        <path d="M0 60 C120 20,180 20,300 60 S480 100,600 60 S780 20,800 60" stroke="white" stroke-width="3" />
        <path d="M0 120 C120 80,180 80,300 120 S480 160,600 120 S780 80,800 120" stroke="white" stroke-width="3" />
        <path d="M0 180 C120 140,180 140,300 180 S480 220,600 180 S780 140,800 180" stroke="white" stroke-width="3" />
      </svg>
      <div class="relative grid md:grid-cols-[1fr_auto] gap-[21px] items-center">
        <div>
          <div class="flex items-center gap-2 mb-[13px] flex-wrap">
            <h1 class="text-[26px] font-bold leading-none">{{ $location->name }}</h1>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/15 backdrop-blur">{{ $overall }}</span>
          </div>
          <p class="text-[12px] text-primary-100/80 mb-[13px]">{{ $location->province }} &middot; {{ $deviceCards->count() }} perangkat terpasang</p>
          <div class="flex gap-[21px]">
            <div><p class="stat-mono text-[24px] font-bold leading-none">{{ $statusCounts['AMAN'] }}</p><p class="text-[11px] text-primary-100/80 mt-1">Aman</p></div>
            <div><p class="stat-mono text-[24px] font-bold leading-none text-[#ffe9ad]">{{ $statusCounts['SIAGA'] }}</p><p class="text-[11px] text-primary-100/80 mt-1">Siaga</p></div>
            <div><p class="stat-mono text-[24px] font-bold leading-none text-[#ffc4c4]">{{ $statusCounts['BAHAYA'] }}</p><p class="text-[11px] text-primary-100/80 mt-1">Bahaya</p></div>
          </div>
        </div>
        <div id="minimap" class="w-full md:w-[130px] h-[100px] md:h-[130px] rounded-xl overflow-hidden ring-2 ring-white/30 shrink-0"></div>
      </div>
    </div>

    {{-- ===================== KARTU DEVICE — SEMUA SENSOR, TANPA GRAFIK ===================== --}}
    <div>
      <h2 class="text-[16px] font-semibold text-neutral-950 mb-[13px]">Perangkat di lokasi ini</h2>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-[13px]">
        @forelse ($deviceCards as $d)
          <a href="{{ route('public.device', [$location->id, $d['id']]) }}" data-device-id="{{ $d['device_id'] }}"
            class="block bg-white rounded-2xl border border-neutral-200 p-[13px] hover:border-primary-300 hover:shadow-sm transition">
            <div class="flex items-start justify-between mb-[13px]">
              <p class="font-semibold text-neutral-950 text-[14px]">{{ $d['name'] }}</p>
              <x-status-badge :status="$d['latest']['status'] ?? null" size="sm" />
            </div>
            @if ($d['sensors']->isNotEmpty())
              <div class="grid grid-cols-2 gap-[8px] pt-[13px] border-t border-neutral-100">
                @foreach ($d['sensors'] as $s)
                  <div class="flex items-center gap-[6px]">
                    <div class="w-6 h-6 rounded-md bg-primary-50 flex items-center justify-center shrink-0">
                      <svg class="w-3 h-3 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        {!! $iconPaths[$s['code']] ?? $fallbackIcon !!}
                      </svg>
                    </div>
                    <span data-field="{{ $s['code'] }}" data-unit="{{ $s['unit'] }}" class="stat-mono text-[12px] font-semibold text-neutral-950 truncate">
                      {{ $s['value'] ?? '-' }}
                    </span>
                  </div>
                @endforeach
              </div>
            @endif
          </a>
        @empty
          <p class="text-sm text-neutral-500 col-span-full text-center py-8">Belum ada perangkat terdaftar.</p>
        @endforelse
      </div>
    </div>

  </main>

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

        const map = L.map('minimap', { zoomControl: false, dragging: false, scrollWheelZoom: false, attributionControl: false })
          .setView([{{ $location->latitude }}, {{ $location->longitude }}], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.circleMarker([{{ $location->latitude }}, {{ $location->longitude }}], {
          radius: 6, fillColor: window.statusStyle('{{ $overall }}').hex, color: '#fff', weight: 2, fillOpacity: 1
        }).addTo(map);

        window.Echo.channel('location.{{ $location->id }}').listen('.sensor.updated', (data) => {
          const card = document.querySelector(`[data-device-id="${data.device_id}"]`);
          if (!card) return;

          window.applyStatusBadge(card.querySelector('.status-badge'), data.status);

          // Sensor pendukung — semua ikut update, bukan cuma TMA & Hujan.
          const readings = data.readings || {};
          Object.keys(readings).forEach((code) => {
            const value = readings[code];
            const field = card.querySelector(`[data-field="${code}"]`);
            if (field && value !== null && value !== undefined) {
              const unit = field.dataset.unit || '';
              field.innerHTML = `${value}<span class="text-[10px] font-sans font-normal text-neutral-400"> ${unit}</span>`;
            }
          });
        });
      });
    </script>
  @endpush

</x-public-layout>
