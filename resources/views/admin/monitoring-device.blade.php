<x-app-layout :title="$device->name ?: $device->device_id">
  @php
    // Ikon per kode sensor - lihat catatan yang sama di public/device.blade.php.
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
          <span id="updated-ago">{{ isset($latest['recorded_at']) ? '' : '-' }}</span>
        </p>
      </div>
      <x-status-badge id="status-pill" :status="$latest['status'] ?? null" size="lg" />
    </div>

    {{-- SENSOR - SEMUA SETARA, PENENTU STATUS DITANDAI --}}
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

    {{-- FOTO KEJADIAN - snapshot terakhir + galeri (khusus admin) --}}
    <div>
      <h2 class="text-[16px] font-semibold text-neutral-950 mb-[13px]">Foto Kejadian</h2>

      @if ($latestPhoto)
        <div id="photo-section-empty" class="hidden bg-white rounded-2xl border border-dashed border-neutral-200 p-[21px] text-center text-[12px] text-neutral-400">
          Belum ada foto tercatat untuk device ini.
        </div>

        <div id="photo-section-content" class="bg-white rounded-2xl border border-neutral-200 p-[21px]">
          <div class="flex flex-col sm:flex-row gap-[21px]">

            <div class="sm:w-2/5 shrink-0">
              <button type="button" id="latest-photo-btn" class="block w-full rounded-xl overflow-hidden border border-neutral-200 hover:opacity-90 transition">
                <img id="latest-photo-img" src="{{ $latestPhoto->photo_url }}" class="w-full h-auto object-cover aspect-video" style="width:100%;height:auto;max-height:280px;object-fit:cover;display:block;" alt="Foto kejadian terakhir">
              </button>
              <div class="mt-2 flex items-center gap-2">
                <x-status-badge id="latest-photo-badge" :status="$latestPhoto->status" size="sm" />
                <span id="latest-photo-time" class="text-[11px] text-neutral-500">{{ $latestPhoto->recorded_at->translatedFormat('d M Y, H:i') }}</span>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <p class="text-[12px] text-neutral-500 mb-[8px]">Riwayat foto (<span id="photo-count">{{ $photoHistory->count() }}</span>)</p>
              <div id="photo-gallery-grid" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                @foreach ($photoHistory as $photo)
                  <button type="button" data-lightbox-trigger data-lightbox-src="{{ $photo->photo_url }}" data-lightbox-status="{{ $photo->status }}" data-lightbox-time="{{ $photo->recorded_at->translatedFormat('d M Y, H:i') }}" class="gallery-thumb aspect-square rounded-lg overflow-hidden border border-neutral-200 hover:ring-2 hover:ring-primary-300 transition" style="aspect-ratio:1/1;overflow:hidden;display:block;">
                    <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" alt="Foto kejadian">
                  </button>
                @endforeach
              </div>
            </div>

          </div>
        </div>
      @else
        <div id="photo-section-empty" class="bg-white rounded-2xl border border-dashed border-neutral-200 p-[21px] text-center text-[12px] text-neutral-400">
          Belum ada foto tercatat untuk device ini.
        </div>

        <div id="photo-section-content" class="hidden bg-white rounded-2xl border border-neutral-200 p-[21px]">
          <div class="flex flex-col sm:flex-row gap-[21px]">
            <div class="sm:w-2/5 shrink-0">
              <button type="button" id="latest-photo-btn" class="block w-full rounded-xl overflow-hidden border border-neutral-200 hover:opacity-90 transition">
                <img id="latest-photo-img" src="" class="w-full h-auto object-cover aspect-video" style="width:100%;height:auto;max-height:280px;object-fit:cover;display:block;" alt="Foto kejadian terakhir">
              </button>
              <div class="mt-2 flex items-center gap-2">
                <x-status-badge id="latest-photo-badge" size="sm" />
                <span id="latest-photo-time" class="text-[11px] text-neutral-500"></span>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[12px] text-neutral-500 mb-[8px]">Riwayat foto (<span id="photo-count">0</span>)</p>
              <div id="photo-gallery-grid" class="grid grid-cols-4 sm:grid-cols-6 gap-2"></div>
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- LIGHTBOX FOTO - div polos, buka/tutup pakai class "hidden" Tailwind, tanpa komponen custom --}}
    <div id="photo-lightbox" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 p-4" style="display:none;position:fixed;top:0;right:0;bottom:0;left:0;z-index:50;justify-content:center;align-items:center;background:rgba(0,0,0,0.7);padding:1rem;">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" style="max-width:42rem;max-height:90vh;overflow-y:auto;">
        <div class="p-[21px]">
          <div class="flex items-center justify-between mb-[13px]">
            <div class="flex items-center gap-2">
              <span id="lightbox-badge" class="status-badge inline-flex items-center gap-1.5 rounded-full font-semibold whitespace-nowrap px-2 py-0.5 text-[11px]" data-base-class="status-badge inline-flex items-center gap-1.5 rounded-full font-semibold whitespace-nowrap px-2 py-0.5 text-[11px]">
                <span data-badge-dot class="w-1.5 h-1.5 rounded-full shrink-0"></span>
                <span data-badge-label></span>
              </span>
              <span id="lightbox-time" class="text-[12px] text-neutral-500"></span>
            </div>
            <button type="button" id="lightbox-close-btn" class="text-neutral-400 hover:text-neutral-600">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <img id="lightbox-img" src="" class="w-full h-auto rounded-lg" style="width:100%;height:auto;max-height:70vh;object-fit:contain;display:block;" alt="Foto kejadian">
        </div>
      </div>
    </div>

    {{-- RIWAYAT SENSOR - KARTU TERPISAH --}}
    <div>
      <div class="flex items-center justify-between flex-wrap gap-3 mb-[13px]">
        <h2 class="text-[16px] font-semibold text-neutral-950">Riwayat Sensor</h2>

        <div class="relative">
          <button type="button" data-sensor-range-toggle class="text-[12px] font-medium text-primary-600 inline-flex items-center gap-1.5 bg-neutral-50 border border-neutral-200 rounded-full px-3 py-1.5">
            <span data-sensor-range-label>7 hari terakhir</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
            </svg>
          </button>
          <div data-sensor-range-menu class="hidden absolute right-0 z-20 mt-2 bg-white border border-neutral-200 rounded-xl shadow-lg w-40 overflow-hidden">
            <ul class="p-1.5 text-[13px] text-neutral-700 font-medium">
              @foreach ([1 => '1 hari terakhir', 7 => '7 hari terakhir', 30 => '30 hari terakhir', 90 => '90 hari terakhir', 0 => 'Semua data'] as $days => $label)
                <li>
                  <button type="button" data-sensor-range-option="{{ $days }}" class="w-full text-left px-3 py-2 hover:bg-primary-50 rounded-lg transition">
                    {{ $label }}
                  </button>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

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

        const lightboxEl = document.getElementById('photo-lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxBadge = document.getElementById('lightbox-badge');
        const lightboxTime = document.getElementById('lightbox-time');
        const latestPhotoImg = document.getElementById('latest-photo-img');
        const latestPhotoBtn = document.getElementById('latest-photo-btn');
        const latestPhotoBadge = document.getElementById('latest-photo-badge');
        const latestPhotoTime = document.getElementById('latest-photo-time');
        const photoSectionEmpty = document.getElementById('photo-section-empty');
        const photoSectionContent = document.getElementById('photo-section-content');
        const photoGalleryGrid = document.getElementById('photo-gallery-grid');
        const photoCountEl = document.getElementById('photo-count');

        let currentLatestPhotoStatus = @json($latestPhoto->status ?? null);

        function openLightbox(src, status, timeText) {
          lightboxImg.src = src;
          lightboxTime.textContent = timeText || '';
          window.applyStatusBadge(lightboxBadge, status);
          lightboxEl.classList.remove('hidden');
          lightboxEl.classList.add('flex');
          lightboxEl.style.display = 'flex';
        }

        function closeLightbox() {
          lightboxEl.classList.add('hidden');
          lightboxEl.classList.remove('flex');
          lightboxEl.style.display = 'none';
        }

        document.getElementById('lightbox-close-btn').addEventListener('click', closeLightbox);
        lightboxEl.addEventListener('click', (e) => { if (e.target === lightboxEl) closeLightbox(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });

        document.body.addEventListener('click', (e) => {
          const trigger = e.target.closest('[data-lightbox-trigger]');
          if (!trigger) return;
          openLightbox(trigger.dataset.lightboxSrc, trigger.dataset.lightboxStatus, trigger.dataset.lightboxTime);
        });

        if (latestPhotoBtn) {
          latestPhotoBtn.addEventListener('click', () => {
            if (!latestPhotoImg.src) return;
            openLightbox(latestPhotoImg.src, currentLatestPhotoStatus, latestPhotoTime.textContent.trim());
          });
        }

        function updateLatestPhoto(photoUrl, status, recordedAtIso) {
          if (photoSectionEmpty) photoSectionEmpty.classList.add('hidden');
          if (photoSectionContent) photoSectionContent.classList.remove('hidden');

          if (latestPhotoImg) latestPhotoImg.src = photoUrl;
          currentLatestPhotoStatus = status;
          if (latestPhotoBadge) window.applyStatusBadge(latestPhotoBadge, status);
          if (latestPhotoTime) {
            latestPhotoTime.textContent = new Date(recordedAtIso).toLocaleString('id-ID', {
              day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
            });
          }
        }

        function prependGalleryThumb(photoUrl, status, recordedAtIso) {
          if (!photoGalleryGrid) return;

          const timeText = new Date(recordedAtIso).toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
          });

          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'gallery-thumb aspect-square rounded-lg overflow-hidden border border-neutral-200 hover:ring-2 hover:ring-primary-300 transition';
          btn.style.aspectRatio = '1/1';
          btn.style.overflow = 'hidden';
          btn.style.display = 'block';
          btn.setAttribute('data-lightbox-trigger', '');
          btn.setAttribute('data-lightbox-src', photoUrl);
          btn.setAttribute('data-lightbox-status', status || '');
          btn.setAttribute('data-lightbox-time', timeText);
          const imgEl = document.createElement('img');
          imgEl.src = photoUrl;
          imgEl.className = 'w-full h-full object-cover';
          imgEl.style.width = '100%';
          imgEl.style.height = '100%';
          imgEl.style.objectFit = 'cover';
          imgEl.style.display = 'block';
          imgEl.loading = 'lazy';
          imgEl.alt = 'Foto kejadian';
          btn.appendChild(imgEl);

          photoGalleryGrid.prepend(btn);

          const thumbs = photoGalleryGrid.querySelectorAll('.gallery-thumb');
          if (thumbs.length > 24) thumbs[thumbs.length - 1].remove();
          if (photoCountEl) photoCountEl.textContent = photoGalleryGrid.querySelectorAll('.gallery-thumb').length;
        }

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

          if (data.photo_url) {
            updateLatestPhoto(data.photo_url, data.status, data.recorded_at);
            prependGalleryThumb(data.photo_url, data.status, data.recorded_at);
          }
        });
      });
    </script>
  @endpush

</x-app-layout>
