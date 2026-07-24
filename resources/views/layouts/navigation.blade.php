{{-- ============================= --}}
{{-- TOP NAVBAR (struktur flowbite) --}}
{{-- ============================= --}}
<nav class="fixed top-0 z-50 w-full bg-white border-b border-primary-100">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">

      {{-- Kiri: toggle sidebar (mobile) + brand --}}
      <div class="flex items-center justify-start rtl:justify-end">
        <button @click="sidebarOpen = !sidebarOpen" type="button"
          class="sm:hidden text-primary-700 bg-transparent box-border border border-transparent hover:bg-primary-50 focus:ring-4 focus:ring-primary-100 font-medium leading-5 rounded-base text-sm p-2 focus:outline-none">
          <span class="sr-only">Buka sidebar</span>
          <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10" />
          </svg>
        </button>

        <a href="{{ route('dashboard') }}" class="flex items-center ms-2 md:me-24">
          <div class="w-8 h-8 rounded-xl bg-primary-600 flex items-center justify-center shrink-0 me-3">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 21c-4.97-3.5-8-7.03-8-10.5A8 8 0 0 1 12 3a8 8 0 0 1 8 7.5c0 3.47-3.03 7-8 10.5Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12c1 1.2 2 1.8 3 1.8s2-.6 3-1.8" />
            </svg>
          </div>
          <div class="leading-tight">
            <span class="block text-lg font-semibold text-neutral-950 whitespace-nowrap">SIGAB</span>
            <span class="hidden sm:block text-xs text-primary-600 whitespace-nowrap">Peringatan Dini Banjir</span>
          </div>
        </a>
      </div>

      {{-- Kanan: judul halaman + menu user --}}
      <div class="flex items-center gap-4">
        <h1 class="hidden lg:block text-sm font-semibold text-neutral-950">{{ $title ?? 'Dashboard' }}</h1>

        {{-- x-data LOKAL di sini — tidak bergantung scope Alpine milik parent --}}
        <div class="flex items-center ms-3" x-data="{ userMenuOpen: false }">
          <div class="relative" @click.outside="userMenuOpen = false">
            <button @click="userMenuOpen = !userMenuOpen" type="button"
              class="flex items-center gap-2 text-sm rounded-full focus:ring-4 focus:ring-primary-100">
              <span class="sr-only">Buka menu user</span>
              <span class="hidden sm:inline text-sm font-medium text-primary-700">{{ auth()->user()->name }}</span>
              <span
                class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-semibold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
              </span>
            </button>

            <div x-show="userMenuOpen" x-transition style="display: none;"
              class="absolute right-0 z-50 mt-2 bg-white border border-neutral-200rounded-base shadow-lg w-56">
              <div class="px-4 py-3 border-b border-primary-100">
                <p class="text-sm font-medium text-neutral-950">{{ auth()->user()->name }}</p>
                <p class="text-sm text-primary-600 truncate">{{ auth()->user()->email }}</p>
              </div>
              <div class="p-2">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit"
                    class="w-full text-left inline-flex items-center p-2 text-sm font-medium text-primary-700 hover:bg-primary-50 hover:text-neutral-950 rounded">
                    Keluar
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</nav>
