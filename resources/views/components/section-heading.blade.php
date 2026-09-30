@props(['eyebrow', 'title', 'center' => false])
<div {{ $attributes->class(['reveal max-w-3xl', 'mx-auto text-center' => $center]) }}>
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 class="h-display mt-4 text-4xl sm:text-5xl lg:text-6xl">{{ $title }}</h2>
    @if (isset($slot) && $slot->isNotEmpty())<div class="mt-5 text-lg leading-relaxed text-stone-400">{{ $slot }}</div>@endif
</div>
