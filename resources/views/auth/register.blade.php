<x-guest-layout>

  <h2 class="text-lg font-semibold text-neutral-950 mb-1">Buat Akun Baru</h2>
  <p class="text-sm text-neutral-600 mb-6">Lengkapi data di bawah untuk membuat akun pengelola.</p>

  <form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    {{-- Name --}}
    <div>
      <label for="name" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Nama Lengkap
      </label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
        autocomplete="name" placeholder="Nama sesuai identitas"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
    </div>

    {{-- Email --}}
    <div>
      <label for="email" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Alamat Email
      </label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
        placeholder="nama@instansi.go.id"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
    </div>

    {{-- Password --}}
    <div>
      <label for="password" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Kata Sandi
      </label>
      <input id="password" type="password" name="password" required autocomplete="new-password"
        placeholder="Minimal 8 karakter"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
    </div>

    {{-- Confirm Password --}}
    <div>
      <label for="password_confirmation" class="block mb-1.5 text-sm font-medium text-neutral-950">
        Konfirmasi Kata Sandi
      </label>
      <input id="password_confirmation" type="password" name="password_confirmation" required
        autocomplete="new-password" placeholder="Ulangi kata sandi"
        class="w-full rounded-lg border-primary-200 bg-neutral-100 text-sm text-neutral-950 placeholder:text-primary-400
                       focus:border-primary-500 focus:ring-primary-500/30 transition" />
      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
    </div>

    <button type="submit"
      class="w-full inline-flex justify-center items-center rounded-lg bg-primary-600 px-4 py-2.5
                   text-sm font-semibold text-white shadow-sm shadow-primary-600/30
                   hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:ring-offset-2
                   transition">
      Daftar
    </button>
  </form>

  <p class="text-center text-sm text-neutral-600 mt-6">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-800 transition">
      Masuk di sini
    </a>
  </p>

</x-guest-layout>
