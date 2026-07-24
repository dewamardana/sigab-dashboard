<x-app-layout>

  <div class="max-w-5xl mx-[18rem] space-y-6 mt-[4rem]">
    <div>
      <h2 class="text-lg font-semibold text-neutral-950">Selamat datang, {{ auth()->user()->name }}</h2>
      <p class="text-sm text-neutral-600">Peran: {{ auth()->user()->getRoleNames()->first() ?? '-' }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @forelse ($locations as $loc)
        <a href="{{ route('public.show', $loc['id']) }}"
          class="block card rounded-2xl p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="font-medium text-neutral-950">{{ $loc['name'] }}</p>
            <span
              class="text-xs px-2 py-1 rounded-full font-medium
                        {{ match ($loc['latest']['status'] ?? null) {
                            'BAHAYA' => 'bg-status-bahaya/10 text-status-bahaya',
                            'SIAGA' => 'bg-status-siaga/10 text-status-siaga',
                            'AMAN' => 'bg-status-aman/10 text-status-aman',
                            default => 'bg-primary-100 text-primary-500',
                        } }}">
              {{ $loc['latest']['status'] ?? 'Belum ada data' }}
            </span>
          </div>
          <p class="text-xs text-primary-500">{{ $loc['province'] }}</p>
          @if ($loc['latest'])
            <div class="mt-3 text-xs text-primary-700 grid grid-cols-2 gap-2">
              <div>TMA: <span class="font-medium">{{ $loc['latest']['tma_cm'] }} cm</span></div>
              <div>Hujan: <span class="font-medium">{{ $loc['latest']['hujan_mm'] }} mm</span></div>
            </div>
          @endif
        </a>
      @empty
        <p class="text-sm text-primary-500 col-span-full text-center py-8">Belum ada lokasi yang bisa dipantau.</p>
      @endforelse
    </div>
  </div>

</x-app-layout>
