<x-guest-layout>

  {{-- Session Status --}}
  <x-auth-session-status class="mb-4" :status="session('status')" />

  <h2 class="text-lg font-semibold text-neutral-950 mb-1">Masuk ke Dashboard</h2>
  <p class="text-sm text-neutral-600 mb-6">Silakan masuk untuk mengakses panel pemantauan.</p>

  <form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    {{-- Email --}}
    <div>
      <label for="email" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Alamat Email
      </label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
        autocomplete="username" placeholder="nama@instansi.go.id"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
    </div>

    {{-- Password --}}
    <div>
      <label for="password" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Kata Sandi
      </label>
      <input id="password" type="password" name="password" required autocomplete="current-password"
        placeholder="••••••••"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
    </div>

    {{-- Remember + Forgot --}}
    <div class="flex items-center justify-between">
      <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
        <input id="remember_me" type="checkbox" name="remember"
          class="rounded border-primary-300 text-primary-600 focus:ring-primary-500/40" />
        <span class="text-sm text-primary-800">Ingat saya</span>
      </label>

      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}"
          class="text-sm font-medium text-primary-600 hover:text-primary-800 transition">
          Lupa kata sandi?
        </a>
      @endif
    </div>

    <button type="submit"
      class="w-full inline-flex justify-center items-center rounded-lg bg-primary-600 px-4 py-2.5
                   text-sm font-semibold text-white shadow-sm shadow-primary-600/30
                   hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:ring-offset-2
                   transition">
      Masuk
    </button>
  </form>

  @if (Route::has('register'))
    <p class="text-center text-sm text-neutral-600 mt-6">
      Belum punya akun?
      <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-800 transition">
        Daftar di sini
      </a>
    </p>
  @endif

</x-guest-layout>
