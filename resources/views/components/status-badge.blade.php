{{--
    Badge status AMAN/SIAGA/BAHAYA — dipakai di semua halaman publik &
    dashboard supaya tampilannya konsisten.

    Props:
      status  — 'AMAN' | 'SIAGA' | 'BAHAYA' | null
      size    — 'sm' | 'md' | 'lg' (default 'md')

    data-base-class disimpan supaya window.applyStatusBadge() (resources/js/app.js)
    bisa membangun ulang className saat status berubah lewat Echo, tanpa perlu
    menduplikasi daftar kelas padding/ukuran di JS.
--}}
@props(['status' => null, 'size' => 'md'])
@php
    $styles = [
        'BAHAYA' => ['bg' => 'bg-status-bahaya/10', 'text' => 'text-status-bahaya', 'dot' => 'bg-status-bahaya', 'label' => 'Bahaya'],
        'SIAGA' => ['bg' => 'bg-status-siaga/10', 'text' => 'text-status-siaga', 'dot' => 'bg-status-siaga', 'label' => 'Siaga'],
        'AMAN' => ['bg' => 'bg-status-aman/10', 'text' => 'text-status-aman', 'dot' => 'bg-status-aman', 'label' => 'Aman'],
    ];
    $s = $styles[$status] ?? ['bg' => 'bg-neutral-100', 'text' => 'text-neutral-500', 'dot' => 'bg-neutral-300', 'label' => 'Belum ada data'];
    $pad = match ($size) {
        'sm' => 'px-2 py-0.5 text-[11px]',
        'lg' => 'px-[13px] py-[8px] text-[14px]',
        default => 'px-3 py-1 text-xs',
    };
    $base = "status-badge inline-flex items-center gap-1.5 rounded-full font-semibold whitespace-nowrap {$pad}";
@endphp
<span {{ $attributes->merge(['class' => "{$base} {$s['bg']} {$s['text']}"]) }} data-base-class="{{ $base }}">
    <span data-badge-dot class="w-1.5 h-1.5 rounded-full shrink-0 {{ $s['dot'] }}"></span>
    <span data-badge-label>{{ $s['label'] }}</span>
</span>
