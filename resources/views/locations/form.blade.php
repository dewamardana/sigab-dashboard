@php
  // $errors bawaan Laravel otomatis tersedia di semua view,
  // dipakai untuk tampilkan pesan validasi gagal
@endphp

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama Lokasi</label>
  <input type="text" name="name" value="{{ old('name') }}" required placeholder="mis. Sungai Code - Jogjakarta"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
  <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
</div>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Provinsi</label>
  <input type="text" name="province" value="{{ old('province') }}" placeholder="mis. DI Yogyakarta"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
</div>

<div class="grid grid-cols-2 gap-3">
  <div>
    <label class="block mb-1.5 text-sm font-medium text-neutral-950">Latitude</label>
    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" required placeholder="-7.8014"
      class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
    <x-input-error :messages="$errors->get('latitude')" class="mt-1.5" />
  </div>
  <div>
    <label class="block mb-1.5 text-sm font-medium text-neutral-950">Longitude</label>
    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" required
      placeholder="110.3644"
      class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
    <x-input-error :messages="$errors->get('longitude')" class="mt-1.5" />
  </div>
</div>

<p class="text-xs text-primary-500 -mt-2">
  Tips: buka Google Maps, klik kanan di titik lokasi sungai, salin koordinat yang muncul.
</p>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Deskripsi</label>
  <textarea name="description" rows="2" placeholder="Opsional"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">{{ old('description') }}</textarea>
</div>
