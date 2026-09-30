@php
    $items = $work->media->values();
    $slides = $items->map(fn ($m) => ['type' => $m->type, 'src' => $m->type === 'embed' ? $m->embedUrl() : $m->src(), 'poster' => $m->poster])->all();
    $settings = \App\Models\Setting::values();
@endphp
<x-layouts::app :title="$work->name()" :og-image="$work->cover()?->thumbnail()">
    <section class="container-x pt-32 pb-20 lg:pt-40"
             x-data="{ i: 0, slides: @js($slides) }"
             @keydown.arrow-right.window="i = (i + 1) % slides.length"
             @keydown.arrow-left.window="i = (i - 1 + slides.length) % slides.length">
        <a href="{{ route('works.index') }}" wire:navigate class="inline-flex items-center gap-2 text-[11px] font-semibold tracking-[0.25em] text-stone-400 uppercase hover:text-gold-500"><x-icon name="arrow" class="size-4 rotate-180" /> {{ __('site.works.back') }}</a>

        <div class="mt-10 grid gap-12 lg:grid-cols-[1.15fr_1fr] lg:gap-20">
            {{-- Media viewer --}}
            <div class="reveal">
                <div class="relative aspect-[4/5] overflow-hidden rounded-[1.75rem] bg-[#16110d]">
                    @foreach ($items as $item)
                        <div x-show="i === {{ $loop->index }}" @if(! $loop->first) x-cloak @endif x-transition.opacity.duration.500ms class="absolute inset-0">
                            @if ($item->type === 'image')
                                <img src="{{ $item->src() }}" alt="{{ $work->name() }}" @if(! $loop->first) loading="lazy" @endif class="size-full object-contain">
                            @elseif ($item->type === 'video')
                                <video src="{{ $item->src() }}" @if($item->poster) poster="{{ $item->poster }}" @endif controls playsinline loop preload="none" class="size-full object-contain"
                                       x-effect="i === {{ $loop->index }} ? ($el.muted = true, $el.play().catch(() => {})) : $el.pause()"></video>
                            @elseif ($item->embedUrl())
                                <iframe src="{{ $item->embedUrl() }}" class="size-full" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>
                            @endif
                        </div>
                    @endforeach
                    <template x-if="slides.length > 1">
                        <div>
                            <button @click="i = (i - 1 + slides.length) % slides.length" class="absolute top-1/2 left-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-[#fff]/85 text-[#1c1611] shadow-lg transition hover:bg-[#fff]" aria-label="Previous"><x-icon name="arrow" class="size-4 rotate-180" /></button>
                            <button @click="i = (i + 1) % slides.length" class="absolute top-1/2 right-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-[#fff]/85 text-[#1c1611] shadow-lg transition hover:bg-[#fff]" aria-label="Next"><x-icon name="arrow" class="size-4" /></button>
                            <p class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-[#16110d]/60 px-3 py-1 text-xs text-[#fff] backdrop-blur" x-text="`${i + 1} / ${slides.length}`"></p>
                        </div>
                    </template>
                </div>
                @if ($items->count() > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                        @foreach ($items as $item)
                            <button @click="i = {{ $loop->index }}" class="relative size-20 shrink-0 overflow-hidden rounded-xl border-2 transition" :class="i === {{ $loop->index }} ? 'border-gold-500' : 'border-transparent opacity-70 hover:opacity-100'">
                                @if ($item->thumbnail())
                                    <img src="{{ $item->thumbnail() }}" alt="" loading="lazy" class="size-full object-cover">
                                @else
                                    <span class="grid size-full place-items-center bg-ink-800"><x-icon name="play" class="size-5" /></span>
                                @endif
                                @if ($item->type !== 'image')<span class="absolute inset-0 grid place-items-center bg-[#16110d]/30 text-[#fff]"><x-icon name="play" class="size-5 fill-current" /></span>@endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="lg:sticky lg:top-32 lg:self-start">
                @if ($work->category)<a href="{{ route('works.index', ['category' => $work->category->slug]) }}" wire:navigate class="eyebrow reveal hover:underline">{{ $work->category->tr('name') }}</a>@endif
                <h1 class="h-display reveal mt-4 text-5xl sm:text-6xl">{{ $work->name() }}</h1>
                <div class="reveal mt-6 flex flex-wrap gap-6 text-sm text-stone-400">
                    @if ($work->location)<span class="flex items-center gap-2"><x-icon name="sparkles" class="size-4 text-gold-500" />{{ $work->location }}</span>@endif
                    @if ($work->event_date)<span class="flex items-center gap-2"><x-icon name="calendar" class="size-4 text-gold-500" />{{ $work->event_date->translatedFormat('d F Y') }}</span>@endif
                    <span class="flex items-center gap-2"><x-icon name="eye" class="size-4 text-gold-500" />{{ $work->views }} {{ __('site.works.views') }}</span>
                </div>
                <div class="hairline reveal mt-8"></div>
                @if ($work->tr('description'))
                    <div class="reveal mt-8 text-lg leading-relaxed whitespace-pre-line text-stone-300">{{ $work->tr('description') }}</div>
                @else
                    <p class="reveal mt-8 text-lg leading-relaxed text-stone-300">{{ __('site.about.text') }}</p>
                @endif
                <div class="reveal mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('contact') }}" wire:navigate class="btn-gold">{{ __('site.works.ask') }} <x-icon name="arrow" class="size-4" /></a>
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings['phone']) }}" class="btn-ghost"><x-icon name="phone" class="size-4" /> {{ $settings['phone'] }}</a>
                </div>
                <p class="reveal mt-8 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-stone-400 uppercase"><x-icon name="gift" class="size-4 text-gold-500" /> {{ __('site.hero.badge') }}</p>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="border-t border-white/10 bg-ink-900 py-24">
            <div class="container-x">
                <h2 class="h-display text-center text-4xl sm:text-5xl">{{ __('site.works.related') }}</h2>
                <div class="mt-12 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <a href="{{ route('works.show', $item) }}" wire:navigate class="group relative block aspect-[4/5] overflow-hidden rounded-[1.25rem] bg-ink-800">
                            @if ($item->cover()?->thumbnail())
                                <img src="{{ $item->cover()->thumbnail() }}" alt="{{ $item->name() }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-1000 group-hover:scale-105">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-[#16110d]/80 to-transparent"></div>
                            <p class="absolute right-4 bottom-4 left-4 font-display text-xl leading-tight text-[#f8f2e8]">{{ $item->name() }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts::app>
