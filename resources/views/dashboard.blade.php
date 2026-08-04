<x-app-layout>

  <div class="max-w-5xl mx-auto space-y-6 p-4 sm:ml-64 mt-14">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold text-neutral-950">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-600">Peran: {{ auth()->user()->getRoleNames()->first() ?? '-' }}</p>
      </div>
      <span class="inline-flex items-center gap-1.5 text-[11px] font-mono font-medium text-primary-600 bg-primary-50 border border-primary-100 rounded-full px-3 py-1.5">
        <span class="relative flex h-1.5 w-1.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-primary-500"></span>
        </span>
        LIVE
      </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="location-cards">
      @forelse ($locations as $loc)
        <a href="{{ route('public.show', $loc['id']) }}" data-location-id="{{ $loc['id'] }}"
          class="block card rounded-2xl p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="font-medium text-neutral-950">{{ $loc['name'] }}</p>
            <x-status-badge :status="$loc['latest']['status'] ?? null" size="sm" />
          </div>
          <p class="text-xs text-primary-500 mb-3">{{ $loc['province'] }}</p>
          @if ($loc['latest'])
            <p data-field="recorded_at" class="text-xs text-neutral-400">
              Diperbarui {{ \Carbon\Carbon::parse($loc['latest']['recorded_at'])->diffForHumans() }}
            </p>
          @else
            <p class="text-xs text-neutral-400">Belum ada data masuk.</p>
          @endif
        </a>
      @empty
        <p class="text-sm text-primary-500 col-span-full text-center py-8">Belum ada lokasi yang bisa dipantau.</p>
      @endforelse
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-location-id]').forEach(card => {
          const id = card.dataset.locationId;
          window.Echo.channel(`location.${id}`).listen('.sensor.updated', (data) => {
            window.applyStatusBadge(card.querySelector('.status-badge'), data.status);
            const updatedEl = card.querySelector('[data-field="recorded_at"]');
            if (updatedEl && data.recorded_at) updatedEl.textContent = `Diperbarui ${window.timeAgo(data.recorded_at)}`;
          });
        });
      });
    </script>
  @endpush

</x-app-layout>
