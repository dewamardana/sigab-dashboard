@props(['active' => false])

@php
  $classes = $active
      ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-primary-50 text-primary-700'
      : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 hover:bg-primary-50 hover:text-primary-700 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
  <span class="shrink-0">{{ $icon }}</span>
  <span>{{ $slot }}</span>
</a>
