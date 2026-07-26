{{--
    Panel perbandingan — dipakai di halaman lokasi (public.show) dan halaman
    device (public.device). Sengaja menampilkan satu device + satu parameter
    sekaligus lewat tab, bukan menumpuk banyak garis warna dalam satu grafik.

    Props:
      charts          — array [{code, name, unit, is_core, series:[{device_id, name, data:[{x,y}]}]}]
      devices         — array [{device_id, name, status}] untuk tab device
      defaultDeviceId — device_id yang dipilih pertama kali (opsional)
--}}
@props(['charts', 'devices', 'defaultDeviceId' => null])

<div id="comparison-panel" class="bg-white rounded-2xl border border-neutral-200 p-[21px]">

    @if ($charts->isEmpty() || $devices->isEmpty())
        <p class="text-sm text-neutral-500 text-center py-8">Belum ada data sensor untuk ditampilkan.</p>
    @else
        <div class="flex items-center justify-between flex-wrap gap-3 mb-[13px]">
            <h2 class="text-[16px] font-semibold text-neutral-950">Riwayat &amp; perbandingan perangkat</h2>

            <div class="relative">
                <button type="button" data-range-toggle
                    class="text-[12px] font-medium text-primary-600 inline-flex items-center gap-1.5 bg-neutral-50 border border-neutral-200 rounded-full px-3 py-1.5">
                    <span data-range-label>7 hari terakhir</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div data-range-menu class="hidden absolute right-0 z-20 mt-2 bg-white border border-neutral-200 rounded-xl shadow-lg w-40 overflow-hidden">
                    <ul class="p-1.5 text-[13px] text-neutral-700 font-medium">
                        @foreach ([1 => '1 hari terakhir', 7 => '7 hari terakhir', 30 => '30 hari terakhir', 90 => '90 hari terakhir', 0 => 'Semua data'] as $days => $label)
                            <li>
                                <button type="button" data-range-option="{{ $days }}"
                                    class="w-full text-left px-3 py-2 hover:bg-primary-50 rounded-lg transition">
                                    {{ $label }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-[6px] mb-[8px]" data-device-tabs></div>
        <div class="flex flex-wrap gap-[6px] mb-[21px]" data-metric-tabs></div>

        <div data-chart-el></div>
        <p data-empty-state class="text-[13px] text-neutral-400 text-center py-10" style="display:none">
            Belum ada riwayat untuk kombinasi perangkat &amp; parameter ini.
        </p>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.comparisonPanelApi = window.renderComparisonPanel(document.getElementById('comparison-panel'), {
                        charts: @json($charts->values()),
                        devices: @json($devices->values()),
                        defaultDeviceId: @json($defaultDeviceId),
                    });
                });
            </script>
        @endpush
    @endif
</div>
