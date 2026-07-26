@props(['title' => null])
<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false }">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ isset($title) ? $title . ' - SIGAB' : 'SIGAB — Sistem Informasi & Peringatan Dini Banjir' }}</title>
  <meta name="description"
    content="Pantau status ketinggian air dan curah hujan secara real-time di lokasi rawan banjir.">

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|jetbrains-mono:500,600" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-neutral-100 text-neutral-950">

  {{-- ===================== NAVBAR (glass) ===================== --}}
  <header
    class="sticky top-0 z-50 bg-white/60 backdrop-blur-lg border-b border-white/60 shadow-sm shadow-primary-900/5">
    <nav class="max-w-6xl mx-auto px-4 lg:px-6 h-16 flex items-center justify-between gap-4">

      <a href="{{ route('public.index') }}" class="flex items-center gap-2.5 shrink-0">
        <div class="w-9 h-9 rounded-xl bg-primary-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 21c-4.97-3.5-8-7.03-8-10.5A8 8 0 0 1 12 3a8 8 0 0 1 8 7.5c0 3.47-3.03 7-8 10.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12c1 1.2 2 1.8 3 1.8s2-.6 3-1.8" />
          </svg>
        </div>
        <span class="font-semibold text-neutral-950">SIGAB</span>
      </a>

      {{-- Menu desktop --}}
      <div class="hidden md:flex items-center gap-8 text-sm font-medium text-primary-700">
        <a href="{{ route('public.index') }}#tentang" class="hover:text-neutral-950 transition">Tentang</a>
        <a href="{{ route('public.index') }}#cara-kerja" class="hover:text-neutral-950 transition">Cara Kerja</a>
        <a href="{{ route('public.index') }}#peta" class="hover:text-neutral-950 transition">Peta Pemantauan</a>
      </div>

      <div class="hidden md:flex items-center gap-4 shrink-0">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-mono font-medium text-primary-600">
          <span class="relative flex h-1.5 w-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-primary-500"></span>
          </span>
          LIVE
        </span>
        <a href="{{ route('login') }}" class="btn-primary !px-4 !py-2">
          Masuk Admin
        </a>
      </div>

      {{-- Tombol hamburger mobile --}}
      <button @click="mobileMenuOpen = !mobileMenuOpen"
        class="md:hidden p-2 -mr-2 text-primary-700 active:scale-90 transition-transform">
        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg x-show="mobileMenuOpen" style="display:none" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </nav>

    {{-- Menu mobile --}}
    <div x-show="mobileMenuOpen" x-transition style="display:none"
      class="md:hidden border-t border-white/60 bg-white/80 backdrop-blur-lg px-4 py-3 space-y-1">
      <a href="{{ route('public.index') }}#tentang"
        class="block px-3 py-2 rounded-lg text-sm font-medium text-primary-700 hover:bg-primary-50">Tentang</a>
      <a href="{{ route('public.index') }}#cara-kerja"
        class="block px-3 py-2 rounded-lg text-sm font-medium text-primary-700 hover:bg-primary-50">Cara Kerja</a>
      <a href="{{ route('public.index') }}#peta"
        class="block px-3 py-2 rounded-lg text-sm font-medium text-primary-700 hover:bg-primary-50">Peta Pemantauan</a>
      <a href="{{ route('login') }}" class="btn-primary w-full mt-2">Masuk Admin</a>
    </div>
  </header>

  {{-- ===================== KONTEN ===================== --}}
  {{ $slot }}

  {{-- ===================== FOOTER ===================== --}}
  <footer class="bg-primary-950 text-primary-200 mt-20">
    <div class="max-w-6xl mx-auto px-4 lg:px-6 py-12 grid sm:grid-cols-3 gap-8">
      <div class="sm:col-span-1">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-7 h-7 rounded-lg bg-primary-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 21c-4.97-3.5-8-7.03-8-10.5A8 8 0 0 1 12 3a8 8 0 0 1 8 7.5c0 3.47-3.03 7-8 10.5Z" />
            </svg>
          </div>
          <span class="font-semibold text-white">SIGAB</span>
        </div>
        <p class="text-sm text-primary-300">Sistem Informasi &amp; Peringatan Dini Banjir — memantau ketinggian air dan
          curah hujan secara real-time untuk keselamatan masyarakat.</p>
      </div>

      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-primary-400 mb-3">Navigasi</p>
        <ul class="space-y-2 text-sm text-primary-300">
          <li><a href="{{ route('public.index') }}#tentang" class="hover:text-white transition">Tentang SIGAB</a></li>
          <li><a href="{{ route('public.index') }}#cara-kerja" class="hover:text-white transition">Cara Kerja</a></li>
          <li><a href="{{ route('public.index') }}#peta" class="hover:text-white transition">Peta Pemantauan</a></li>
          <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk Admin</a></li>
        </ul>
      </div>

      <div class="sm:text-right flex flex-col sm:items-end justify-between">
        <div class="inline-flex items-center gap-1.5 text-[11px] font-mono text-primary-400 sm:justify-end">
          <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
          Data disiarkan real-time via Laravel Reverb
        </div>
        <p class="text-sm text-primary-400 mt-4 sm:mt-0">
          &copy; {{ date('Y') }} SIGAB. Dikembangkan untuk pemantauan banjir Bali &amp; Jogjakarta.
        </p>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>

</html>
