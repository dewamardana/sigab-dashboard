<x-public-layout>

  @php
    $statusCounts = ['AMAN' => 0, 'SIAGA' => 0, 'BAHAYA' => 0];
    foreach ($locations as $loc) {
        $st = $loc['latest']['status'] ?? null;
        if (isset($statusCounts[$st])) {
            $statusCounts[$st]++;
        }
    }
  @endphp

  {{-- ===================== PITA PERINGATAN (hanya tampil kalau ada BAHAYA) ===================== --}}
  <div id="bahaya-alert" class="bg-status-bahaya text-white" @if ($statusCounts['BAHAYA'] === 0) style="display:none" @endif>
    <a href="#peta"
      class="max-w-6xl mx-auto px-4 lg:px-6 py-2.5 flex items-center justify-center gap-2 text-sm font-semibold hover:bg-black/5 transition">
      <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
      </svg>
      <span id="bahaya-alert-text"><span class="stat-mono">{{ $statusCounts['BAHAYA'] }}</span> lokasi berstatus BAHAYA
        saat ini — lihat peta</span>
      <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </a>
  </div>

  {{-- ===================== HERO ===================== --}}
  <section class="relative overflow-hidden">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-300/40 rounded-full blur-3xl"></div>
    <div class="absolute -top-10 right-0 w-80 h-80 bg-primary-200/50 rounded-full blur-3xl"></div>
    <div class="absolute top-40 left-1/2 w-72 h-72 bg-primary-100/60 rounded-full blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-4 lg:px-6 pt-14 pb-20 lg:pt-20 lg:pb-28 text-center">
      <span
        class="inline-block text-xs font-semibold tracking-wider uppercase text-primary-600 bg-white/70 backdrop-blur-xl border border-white/60 px-4 py-1.5 rounded-full mb-5">
        Peringatan Dini &bull; Real-Time &bull; Terbuka untuk Publik
      </span>
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-neutral-950 leading-tight max-w-3xl mx-auto">
        Pantau Risiko Banjir di Wilayah Anda, Kapan Saja
      </h1>
      <p class="mt-5 text-base sm:text-lg text-neutral-600 max-w-2xl mx-auto">
        SIGAB memantau ketinggian air dan curah hujan secara otomatis dari sensor lapangan,
        lalu menyiarkan status AMAN, SIAGA, atau BAHAYA secara langsung ke masyarakat dan petugas.
      </p>

      {{-- Ringkasan status live — angka nyata, bukan dekorasi --}}
      <div class="mt-8 inline-flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
        <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur border border-white/60 rounded-full pl-3 pr-4 py-1.5 text-sm shadow-sm">
          <span class="w-2 h-2 rounded-full bg-status-aman"></span>
          <span id="stat-aman" class="stat-mono font-semibold text-neutral-950">{{ $statusCounts['AMAN'] }}</span>
          <span class="text-neutral-500">Aman</span>
        </span>
        <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur border border-white/60 rounded-full pl-3 pr-4 py-1.5 text-sm shadow-sm">
          <span class="w-2 h-2 rounded-full bg-status-siaga"></span>
          <span id="stat-siaga" class="stat-mono font-semibold text-neutral-950">{{ $statusCounts['SIAGA'] }}</span>
          <span class="text-neutral-500">Siaga</span>
        </span>
        <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur border border-white/60 rounded-full pl-3 pr-4 py-1.5 text-sm shadow-sm">
          <span class="w-2 h-2 rounded-full bg-status-bahaya"></span>
          <span id="stat-bahaya" class="stat-mono font-semibold text-neutral-950">{{ $statusCounts['BAHAYA'] }}</span>
          <span class="text-neutral-500">Bahaya</span>
        </span>
      </div>

      <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="#peta" class="btn-primary w-full sm:w-auto">Lihat Peta Pemantauan</a>
        <a href="#tentang" class="btn-secondary w-full sm:w-auto">Pelajari Lebih Lanjut</a>
      </div>
    </div>
  </section>

  {{-- ===================== BENTO: CARDS FITUR ===================== --}}
  <section class="max-w-6xl mx-auto px-4 lg:px-6 -mt-14 relative z-10">
    <div class="grid lg:grid-cols-3 gap-5">

      <div class="glass-card p-8 lg:col-span-2 flex flex-col justify-center">
        <div class="w-14 h-14 rounded-2xl bg-primary-100 flex items-center justify-center mb-5">
          <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-neutral-950 mb-2">Data Real-Time</h3>
        <p class="text-sm text-neutral-600 max-w-md">Status dan grafik ter-update otomatis begitu sensor mengirim data
          baru — tanpa perlu refresh halaman, langsung terlihat perubahannya di peta dan grafik.</p>
      </div>

      <div class="grid grid-rows-2 gap-5">
        <div class="glass-card p-6">
          <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <h3 class="font-semibold text-neutral-950 mb-1 text-sm">Notifikasi Otomatis</h3>
          <p class="text-xs text-neutral-600">Petugas menerima peringatan Telegram instan saat status berubah.</p>
        </div>

        <div class="glass-card p-6">
          <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="font-semibold text-neutral-950 mb-1 text-sm">Terbuka untuk Semua Lokasi</h3>
          <p class="text-xs text-neutral-600">Lokasi baru otomatis tampil di peta tanpa update sistem.</p>
        </div>
      </div>

    </div>
  </section>

  {{-- ===================== TENTANG / TUJUAN (BENTO) ===================== --}}
  <section id="tentang" class="max-w-6xl mx-auto px-4 lg:px-6 py-20 scroll-mt-20">
    <div class="grid lg:grid-cols-2 gap-6 items-center">
      <div class="card p-8">
        <span class="text-xs font-semibold tracking-wider uppercase text-primary-600">Tentang SIGAB</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-neutral-950 mt-2 mb-4">
          Membantu Masyarakat Bersiap Lebih Awal
        </h2>
        <p class="text-sm sm:text-base text-neutral-600 leading-relaxed mb-4">
          Banjir sering datang tanpa peringatan yang cukup. SIGAB hadir untuk menjembatani
          kesenjangan itu — memasang sensor ketinggian air dan curah hujan di titik-titik
          rawan, lalu mengolah datanya secara otomatis menjadi status yang mudah dipahami:
          <span class="font-medium text-status-aman">AMAN</span>,
          <span class="font-medium text-status-siaga">SIAGA</span>, atau
          <span class="font-medium text-status-bahaya">BAHAYA</span>.
        </p>
        <p class="text-sm sm:text-base text-neutral-600 leading-relaxed">
          Informasi ini terbuka untuk siapa saja — warga, relawan, dan petugas — sehingga
          keputusan evakuasi bisa diambil lebih cepat dan tepat waktu.
        </p>
      </div>
      <div class="grid grid-cols-2 gap-5">
        <div class="card p-6 text-center flex flex-col justify-center">
          <p class="stat-mono text-4xl font-bold text-primary-700">24/7</p>
          <p class="text-xs text-neutral-500 mt-1.5">Pemantauan berkelanjutan</p>
        </div>
        <div class="card p-6 text-center flex flex-col justify-center">
          <p class="stat-mono text-4xl font-bold text-primary-700">&lt;1 mnt</p>
          <p class="text-xs text-neutral-500 mt-1.5">Kecepatan notifikasi</p>
        </div>
        <div class="card p-6 text-center col-span-2 flex flex-col justify-center">
          <p class="text-3xl font-bold text-primary-700">Bali &amp; Jogjakarta</p>
          <p class="text-xs text-neutral-500 mt-1.5">Wilayah pemantauan saat ini</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== CARA KERJA ===================== --}}
  <section id="cara-kerja" class="bg-white py-20 scroll-mt-20">
    <div class="max-w-6xl mx-auto px-4 lg:px-6">
      <div class="text-center max-w-xl mx-auto mb-12">
        <span class="text-xs font-semibold tracking-wider uppercase text-primary-600">Cara Kerja</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-neutral-950 mt-2">Dari Sensor Sampai Peringatan</h2>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ([['no' => '1', 'title' => 'Sensor Membaca Data', 'desc' => 'Perangkat di lapangan membaca tinggi air dan curah hujan setiap saat.'], ['no' => '2', 'title' => 'Dikirim ke Sistem', 'desc' => 'Data dikirim otomatis lewat jaringan ke server pusat SIGAB.'], ['no' => '3', 'title' => 'Dihitung Statusnya', 'desc' => 'Sistem membandingkan data dengan ambang batas AMAN/SIAGA/BAHAYA.'], ['no' => '4', 'title' => 'Disiarkan Real-Time', 'desc' => 'Status ditampilkan di peta ini dan notifikasi dikirim ke petugas.']] as $step)
          <div class="bg-primary-50/70 rounded-3xl p-6 border border-primary-100">
            <div
              class="stat-mono w-10 h-10 rounded-2xl bg-primary-600 text-white flex items-center justify-center font-semibold text-sm mb-4">
              {{ $step['no'] }}
            </div>
            <h3 class="font-semibold text-neutral-950 mb-1.5 text-sm">{{ $step['title'] }}</h3>
            <p class="text-xs text-neutral-600 leading-relaxed">{{ $step['desc'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== PETA & LOKASI (BENTO) ===================== --}}
  <section id="peta" class="max-w-6xl mx-auto px-4 lg:px-6 py-20 scroll-mt-20">
    <div class="text-center max-w-xl mx-auto mb-10">
      <span class="text-xs font-semibold tracking-wider uppercase text-primary-600">Peta Pemantauan</span>
      <h2 class="text-2xl sm:text-3xl font-bold text-neutral-950 mt-2">Status Lokasi Saat Ini</h2>
      <p class="text-sm text-neutral-600 mt-2">Pilih salah satu lokasi untuk melihat grafik lengkap.</p>
    </div>

    <div class="grid lg:grid-cols-5 gap-5 mb-8">
      <div class="lg:col-span-3 card p-4 sm:p-5">
        <div id="map" class="rounded-2xl overflow-hidden w-full aspect-[4/5] sm:aspect-[3/2] lg:aspect-[16/10]">
        </div>
      </div>
      <div class="lg:col-span-2 card p-8 flex flex-col justify-center gap-6">
        <div>
          <p class="stat-mono text-5xl font-bold text-primary-700">{{ $locations->count() }}</p>
          <p class="text-sm text-neutral-500 mt-1.5">Lokasi aktif dipantau saat ini</p>
        </div>
        <div class="pt-5 border-t border-neutral-100 space-y-3">
          <p class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-1">Legenda Status</p>
          <div class="flex items-center gap-2.5 text-sm text-neutral-700">
            <span class="w-3 h-3 rounded-full bg-status-aman shrink-0"></span> Aman
          </div>
          <div class="flex items-center gap-2.5 text-sm text-neutral-700">
            <span class="w-3 h-3 rounded-full bg-status-siaga shrink-0"></span> Siaga — waspada
          </div>
          <div class="flex items-center gap-2.5 text-sm text-neutral-700">
            <span class="w-3 h-3 rounded-full bg-status-bahaya shrink-0"></span> Bahaya — segera evakuasi
          </div>
        </div>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5" id="location-cards">
      @forelse ($locations as $loc)
        <a href="{{ route('public.show', $loc['id']) }}" id="card-{{ $loc['id'] }}"
          class="group card p-6 hover:shadow-[0_16px_40px_rgb(4,15,15,0.08)] hover:-translate-y-1 transition-all duration-200">
          <div class="flex items-start justify-between mb-4">
            <div>
              <p class="font-semibold text-neutral-950 group-hover:text-primary-700 transition">{{ $loc['name'] }}</p>
              <p class="text-xs text-neutral-500 mt-0.5">{{ $loc['province'] }}</p>
            </div>
            <x-status-badge :status="$loc['latest']['status'] ?? null" size="sm" class="shrink-0" />
          </div>

          @if ($loc['latest'])
            <p class="text-xs text-neutral-500 mb-4">
              Diperbarui {{ \Carbon\Carbon::parse($loc['latest']['recorded_at'])->diffForHumans() }}
            </p>
          @endif

          <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
            <div class="flex items-center gap-1.5 text-xs text-neutral-500">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              {{ $loc['device_count'] }} perangkat
            </div>
            <span
              class="text-primary-600 group-hover:translate-x-1 transition-transform text-sm font-medium inline-flex items-center gap-1">
              Lihat detail
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </span>
          </div>
        </a>
      @empty
        <p class="text-sm text-neutral-500 col-span-full text-center py-8">Belum ada lokasi terdaftar.</p>
      @endforelse
    </div>
  </section>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('map').setView([-2.5, 118], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {};
        const locations = @json($locations);
        const statusByLocation = {};

        locations.forEach(loc => {
          statusByLocation[loc.id] = loc.latest?.status ?? null;
          if (!loc.latitude || !loc.longitude) return;

          const style = window.statusStyle(loc.latest?.status);
          const marker = L.circleMarker([loc.latitude, loc.longitude], {
            radius: 10,
            fillColor: style.hex,
            color: '#fff',
            weight: 2,
            fillOpacity: 0.9
          }).addTo(map).bindPopup(`<b>${loc.name}</b><br>Status: ${style.label}`);
          markers[loc.id] = marker;
        });

        function recomputeCounts() {
          const counts = { AMAN: 0, SIAGA: 0, BAHAYA: 0 };
          Object.values(statusByLocation).forEach(s => { if (counts[s] !== undefined) counts[s]++; });

          document.getElementById('stat-aman').textContent = counts.AMAN;
          document.getElementById('stat-siaga').textContent = counts.SIAGA;
          document.getElementById('stat-bahaya').textContent = counts.BAHAYA;

          const banner = document.getElementById('bahaya-alert');
          const bannerText = document.getElementById('bahaya-alert-text');
          if (counts.BAHAYA > 0) {
            banner.style.display = '';
            bannerText.innerHTML = `<span class="stat-mono">${counts.BAHAYA}</span> lokasi berstatus BAHAYA saat ini — lihat peta`;
          } else {
            banner.style.display = 'none';
          }
        }

        locations.forEach(loc => {
          window.Echo.channel(`location.${loc.id}`).listen('.sensor.updated', (data) => {
            statusByLocation[loc.id] = data.status;
            recomputeCounts();

            const style = window.statusStyle(data.status);
            if (markers[loc.id]) {
              markers[loc.id].setStyle({ fillColor: style.hex });
              markers[loc.id].setPopupContent(`<b>${loc.name}</b><br>Status: ${style.label}`);
            }

            const card = document.getElementById(`card-${loc.id}`);
            if (card) {
              window.applyStatusBadge(card.querySelector('.status-badge'), data.status);
            }
          });
        });
      });
    </script>
  @endpush

</x-public-layout>
