<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Device ID</label>
  <input type="text" name="device_id" value="{{ old('device_id') }}" required
    placeholder="mis. SIGAB-Jogja-001 (harus cocok field 'device' di JSON MQTT)"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
  <x-input-error :messages="$errors->get('device_id')" class="mt-1.5" />
</div>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Nama Perangkat</label>
  <input type="text" name="name" value="{{ old('name') }}" placeholder="mis. Sensor Jembatan Kali Code"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
</div>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Lokasi</label>
  <select name="location_id" required
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
    <option value="">-- Pilih Lokasi --</option>
    @foreach ($locations as $loc)
      <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}
      </option>
    @endforeach
  </select>
  <x-input-error :messages="$errors->get('location_id')" class="mt-1.5" />
</div>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Telegram Chat ID (opsional)</label>
  <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id') }}"
    placeholder="Kosongkan untuk pakai default sistem"
    class="w-full rounded-lg border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500/30">
</div>

<div>
  <label class="block mb-1.5 text-sm font-medium text-neutral-950">Sensor yang Dimiliki</label>
  <div class="grid grid-cols-2 gap-2">
    @foreach ($sensorTypes as $type)
      <label class="flex items-center gap-2 text-sm text-primary-800">
        <input type="checkbox" name="sensor_type_ids[]" value="{{ $type->id }}"
          {{ in_array($type->id, old('sensor_type_ids', [])) ? 'checked' : '' }}
          class="rounded border-primary-300 text-primary-600">
        {{ $type->name }}
      </label>
    @endforeach
  </div>
</div>
