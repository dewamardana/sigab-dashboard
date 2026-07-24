<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name', 'SIGAB') }}</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-neutral-100">

  {{-- Overlay mobile saat sidebar terbuka --}}
  <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-primary-950/40 lg:hidden" style="display: none;"></div>

  {{-- Sidebar --}}
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-neutral-200transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">

    <div class="h-16 flex items-center gap-3 px-5 border-b border-primary-100">
      <div class="w-9 h-9 rounded-xl bg-primary-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 21c-4.97-3.5-8-7.03-8-10.5A8 8 0 0 1 12 3a8 8 0 0 1 8 7.5c0 3.47-3.03 7-8 10.5Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12c1 1.2 2 1.8 3 1.8s2-.6 3-1.8" />
        </svg>
      </div>
      <div>
        <p class="text-sm font-semibold text-neutral-950 leading-tight">SIGAB</p>
        <p class="text-xs text-primary-600 leading-tight">Peringatan Dini Banjir</p>
      </div>
    </div>

    <nav class="px-3 py-4 space-y-1 overflow-y-auto" style="height: calc(100vh - 4rem);">

      <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <x-slot name="icon">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </x-slot>
        Dashboard
      </x-nav-link-sidebar>

      @role('superadmin')
        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-primary-400 uppercase tracking-wider">Manajemen</p>

        <x-nav-link-sidebar href="#" :active="false">
          <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </x-slot>
          Manajemen Lokasi
        </x-nav-link-sidebar>

        <x-nav-link-sidebar href="#" :active="false">
          <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </x-slot>
          Manajemen Perangkat
        </x-nav-link-sidebar>

        <x-nav-link-sidebar href="#" :active="false">
          <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </x-slot>
          Manajemen User
        </x-nav-link-sidebar>
      @endrole

      <p class="px-3 pt-4 pb-1 text-xs font-semibold text-primary-400 uppercase tracking-wider">Data</p>

      <x-nav-link-sidebar href="#" :active="false">
        <x-slot name="icon">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
          </svg>
        </x-slot>
        Riwayat &amp; Laporan
      </x-nav-link-sidebar>

      <x-nav-link-sidebar href="#" :active="false">
        <x-slot name="icon">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </x-slot>
        Log Notifikasi
      </x-nav-link-sidebar>
    </nav>
  </aside>

  {{-- Konten utama --}}
  <div class="lg:pl-64">

    {{-- Topbar --}}
    <header
      class="h-16 bg-white border-b border-neutral-200flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20">
      <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-primary-700 p-2 -ml-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <h1 class="text-base font-semibold text-neutral-950">{{ $title ?? 'Dashboard' }}</h1>

      <div class="flex items-center gap-3">
        <span class="hidden sm:inline text-sm text-primary-700">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition">
            Keluar
          </button>
        </form>
      </div>
    </header>

    {{-- Slot konten halaman --}}
    <main class="p-4 lg:p-6">
      {{ $slot }}
    </main>
  </div>

  @stack('scripts')
</body>

</html>
