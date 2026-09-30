@props(['eyebrow', 'title'])
<section class="relative overflow-hidden pt-40 pb-16 text-center">
    <div class="pointer-events-none absolute -top-40 left-1/2 size-[40rem] -translate-x-1/2 rounded-full bg-gold-200/60 blur-3xl"></div>
    <div class="container-x relative">
        <p class="eyebrow reveal">{{ $eyebrow }}</p>
        <h1 class="h-display reveal mx-auto mt-5 max-w-4xl text-5xl sm:text-7xl">{{ $title }}</h1>
        @if ($slot->isNotEmpty())<div class="reveal mx-auto mt-6 max-w-2xl text-lg text-stone-400">{{ $slot }}</div>@endif
        <div class="reveal mx-auto mt-10 flex max-w-xs items-center gap-4 text-gold-500">
            <span class="hairline"></span><span class="text-lg">✦</span><span class="hairline"></span>
        </div>
    </div>
</section>
