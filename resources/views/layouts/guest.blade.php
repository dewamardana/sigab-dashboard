<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">

    <div class="w-full max-w-md">

      {{-- Logo & Nama Sistem --}}
      <div class="flex flex-col items-center mb-8">
        <div
          class="w-14 h-14 rounded-2xl bg-primary-600 flex items-center justify-center shadow-lg shadow-primary-600/20 mb-4">
          <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 21c-4.97-3.5-8-7.03-8-10.5A8 8 0 0 1 12 3a8 8 0 0 1 8 7.5c0 3.47-3.03 7-8 10.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12c1 1.2 2 1.8 3 1.8s2-.6 3-1.8" />
          </svg>
        </div>
        <h1 class="text-xl font-semibold text-primary-900">SIGAB</h1>
        <p class="text-sm text-primary-600">Sistem Informasi &amp; Peringatan Dini Banjir</p>
      </div>

      {{-- Card Form --}}
      <div
        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-primary-900/5 ring-1 ring-primary-900/5 p-8">
        {{ $slot }}
      </div>

      <p class="text-center text-xs text-primary-700/60 mt-6">
        &copy; {{ date('Y') }} SIGAB — Dikembangkan untuk pemantauan banjir Bali &amp; Jogjakarta
      </p>
    </div>
  </div>
</body>

</html>
