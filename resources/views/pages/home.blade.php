@php
    $slides = $heroImages->isNotEmpty() ? $heroImages->all() : ['/og.jpg'];
    $settings = \App\Models\Setting::values();
    $heroVideo = $settings['hero_video'];
    $instagram = $settings['instagram'];
    $brands = ['Le Labo', 'Kilian Paris', 'Creed', 'Xerjoff', 'Parfums de Marly', 'Amouage', 'Maison Francis Kurkdjian', 'Clive Christian', 'Montale', 'Mancera', 'Memo Paris', 'Chabaud', 'Essential Parfums', 'Matière Première', 'Electimuss', 'BY CALONE'];
    $aboutA = $featured[3] ?? $featured[0] ?? null;
    $aboutB = $featured[4] ?? $featured[1] ?? null;
@endphp
<x-layouts::app>
    {{-- HERO --}}
    <section class="grain relative overflow-hidden"
             x-data="{ i: 0, slides: @js($slides) }"
             x-init="slides.length > 1 && setInterval(() => i = (i + 1) % slides.length, 5500)">
        <div class="pointer-events-none absolute -top-60 -left-40 size-[44rem] rounded-full bg-gold-200/70 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 -bottom-60 size-[40rem] rounded-full bg-ink-700/80 blur-3xl"></div>

        <div class="container-x relative grid min-h-[100svh] items-center gap-12 pt-28 pb-16 lg:grid-cols-[1.05fr_1fr] lg:gap-20">
            <div class="relative z-10 text-center lg:text-left">
                <p class="eyebrow reveal">{{ __('site.hero.eyebrow') }}</p>
                <h1 class="h-display reveal mt-6 text-[2.5rem] break-words sm:text-7xl xl:text-[5.6rem]">{{ __('site.hero.title') }}</h1>
                <div class="reveal mx-auto mt-8 flex max-w-xs items-center gap-4 text-gold-500 lg:mx-0">
                    <span class="hairline"></span><span>✦</span><span class="hairline"></span>
                </div>
                <p class="reveal mx-auto mt-8 max-w-xl text-lg leading-relaxed text-stone-300 lg:mx-0">{{ __('site.hero.text') }}</p>
                <div class="reveal mt-10 flex flex-wrap justify-center gap-4 lg:justify-start">
                    <a href="{{ route('works.index') }}" wire:navigate class="btn-gold">{{ __('site.hero.cta_secondary') }} <x-icon name="arrow" class="size-4" /></a>
                    <a href="{{ route('contact') }}" wire:navigate class="btn-ghost">{{ __('site.hero.cta') }}</a>
                </div>
                <p class="reveal mt-10 inline-flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-stone-400 uppercase">
                    <x-icon name="gift" class="size-4 text-gold-500" /> {{ __('site.hero.badge') }}
                </p>
            </div>

            <div class="reveal relative mx-auto w-full max-w-[30rem]">
                <div class="arch absolute -inset-2 border border-gold-500/40 sm:-inset-4"></div>
                <div class="arch relative aspect-[3/4] overflow-hidden bg-ink-800 shadow-[0_60px_90px_-50px_rgb(60_40_20/0.6)]">
                    @if ($heroVideo)
                        <video class="size-full object-cover" src="{{ $heroVideo }}" autoplay muted loop playsinline></video>
                    @else
                        <template x-for="(src, index) in slides" :key="index">
                            <img :src="src" alt="" class="absolute inset-0 size-full object-cover transition-all duration-[1800ms] ease-out"
                                 :class="i === index ? 'opacity-100 scale-100' : 'opacity-0 scale-110'">
                        </template>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#16110d]/30 to-transparent"></div>
                </div>
                <div class="animate-float absolute -bottom-8 -left-6 hidden rounded-full bg-ink-900 p-4 shadow-2xl sm:block">
                    <img src="/logo.svg" alt="" class="size-20">
                </div>
                <div class="absolute top-1/2 -right-3 flex -translate-y-1/2 flex-col gap-2">
                    <template x-for="(src, index) in slides" :key="'d' + index">
                        <button @click="i = index" class="w-1.5 rounded-full transition-all" :class="i === index ? 'h-8 bg-gold-500' : 'h-1.5 bg-stone-400/50'" :aria-label="index + 1"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    {{-- BRANDS --}}
    <div class="border-y border-white/10 bg-ink-900 py-6">
        <div class="relative overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_10%,#000_90%,transparent)]">
            <div class="animate-marquee flex w-max items-center gap-12 whitespace-nowrap font-display text-2xl text-stone-300 italic sm:text-3xl">
                @foreach (range(1, 2) as $_)
                    @foreach ($brands as $brand)
                        <span class="flex items-center gap-12">{{ $brand }} <span class="text-base text-gold-500 not-italic">✦</span></span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- ABOUT --}}
    <section id="about" class="relative py-28 lg:py-36">
        <div class="container-x grid items-center gap-16 lg:grid-cols-2 lg:gap-24">
            <div class="reveal relative order-2 lg:order-1">
                <div class="grid grid-cols-2 items-end gap-5">
                    @if ($aboutA)
                        <a href="{{ route('works.show', $aboutA) }}" wire:navigate class="arch block aspect-[3/4] overflow-hidden"><img src="{{ $aboutA->cover()->src() }}" alt="{{ $aboutA->name() }}" loading="lazy" class="size-full object-cover transition duration-1000 hover:scale-105"></a>
                    @endif
                    @if ($aboutB)
                        <a href="{{ route('works.show', $aboutB) }}" wire:navigate class="block aspect-[3/4] -translate-y-16 overflow-hidden rounded-[1.5rem]"><img src="{{ $aboutB->cover()->src() }}" alt="{{ $aboutB->name() }}" loading="lazy" class="size-full object-cover transition duration-1000 hover:scale-105"></a>
                    @endif
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <x-section-heading :eyebrow="__('site.about.eyebrow')" :title="__('site.about.title')">
                    {{ __('site.about.text') }}
                </x-section-heading>
                <ul class="reveal mt-10 space-y-5 border-t border-white/10 pt-8">
                    @foreach (__('site.about.points') as $point)
                        <li class="flex items-start gap-4 text-stone-300">
                            <span class="mt-1 text-gold-500">✦</span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
                @if ($stats)
                    <div class="reveal mt-12 flex flex-wrap gap-12">
                        @foreach ($stats as $key => $value)
                            <div x-data="{ n: 0 }"
                                 x-intersect.once="let s = null; const step = (t) => { s ??= t; const p = Math.min((t - s) / 1800, 1); n = Math.floor({{ $value }} * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(step) }; requestAnimationFrame(step)">
                                <p class="font-display text-6xl text-stone-50"><span x-text="n >= 1000 ? (n / 1000).toFixed(1).replace('.0', '') + 'k' : n">{{ $value }}</span><span class="text-gold-500">+</span></p>
                                <p class="mt-1 text-[11px] font-semibold tracking-[0.2em] text-stone-400 uppercase">{{ __('site.stats.'.$key) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- QUOTE --}}
    <section class="theme-noir grain relative overflow-hidden bg-ink-950 py-28 text-center">
        @if ($featured->isNotEmpty())
            <img src="{{ $featured->last()->cover()->src() }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover opacity-20">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-ink-950/70 via-ink-950/60 to-ink-950"></div>
        <div class="container-x relative">
            <span class="reveal block font-display text-7xl leading-none text-gold-500">“</span>
            <blockquote class="reveal mx-auto max-w-4xl font-display text-4xl leading-tight text-stone-50 italic sm:text-6xl">{{ __('site.quote.text') }}</blockquote>
            <p class="reveal mt-8 text-[11px] font-semibold tracking-[0.35em] text-gold-400 uppercase">{{ __('site.quote.author') }}</p>
        </div>
    </section>

    {{-- COLLECTION --}}
    <section class="py-28 lg:py-36">
        <div class="container-x">
            <x-section-heading :eyebrow="__('site.works.eyebrow')" :title="__('site.works.title')" center>{{ __('site.works.text') }}</x-section-heading>
            <div class="mt-14">
                <livewire:portfolio-grid :per-page="8" />
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('works.index') }}" wire:navigate class="text-[11px] font-semibold tracking-[0.3em] text-gold-500 uppercase hover:underline">{{ __('site.works.view') }} →</a>
            </div>
        </div>
    </section>

    {{-- BY CALONE --}}
    @if ($byCalone->isNotEmpty())
        <section class="theme-noir grain relative overflow-hidden bg-ink-950 py-28 lg:py-36">
            <div class="pointer-events-none absolute top-0 right-0 size-[36rem] rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="container-x relative grid items-center gap-16 lg:grid-cols-[1fr_1.3fr]">
                <div>
                    <p class="eyebrow reveal">{{ __('site.by_calone.eyebrow') }}</p>
                    <h2 class="h-display reveal mt-5 text-5xl sm:text-6xl"><span class="text-gold-gradient">BY CALONE</span><br><span class="text-3xl italic sm:text-4xl">Find Your Star</span></h2>
                    <p class="reveal mt-6 max-w-md text-lg leading-relaxed text-stone-300">{{ __('site.by_calone.text') }}</p>
                    <a href="{{ route('works.index', ['category' => 'by-calone']) }}" wire:navigate class="btn-gold reveal mt-10">{{ __('site.by_calone.cta') }} <x-icon name="arrow" class="size-4" /></a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($byCalone as $item)
                        <a href="{{ route('works.show', $item) }}" wire:navigate
                           @class(['reveal group relative block overflow-hidden', 'arch aspect-[3/4]' => $loop->even, 'aspect-[3/4] rounded-[1.5rem] translate-y-10' => $loop->odd])>
                            @if ($item->cover()?->thumbnail())
                                <img src="{{ $item->cover()->type === 'image' ? $item->cover()->src() : $item->cover()->thumbnail() }}" alt="{{ $item->name() }}" loading="lazy" class="size-full object-cover transition duration-[1400ms] group-hover:scale-105">
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- SERVICES --}}
    @if ($services->isNotEmpty())
        <section id="boutique" class="scroll-mt-20 py-28 lg:py-36">
            <div class="container-x">
                <x-section-heading :eyebrow="__('site.services.eyebrow')" :title="__('site.services.title')" center />
                <div class="mt-16 grid border-t border-l border-white/10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <article class="reveal group relative border-r border-b border-white/10 p-10 transition duration-500 hover:bg-ink-900" style="transition-delay: {{ $loop->index * 60 }}ms">
                            <span class="font-display text-sm text-gold-500">0{{ $loop->iteration }}</span>
                            <x-icon :name="$service->icon" class="mt-6 size-8 text-gold-500 transition duration-500 group-hover:-translate-y-1" />
                            <h3 class="mt-6 font-display text-3xl text-stone-50">{{ $service->tr('title') }}</h3>
                            <p class="mt-3 leading-relaxed text-stone-400">{{ $service->tr('description') }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- VIDEOS --}}
    <section class="bg-ink-900 py-28 lg:py-36">
        <div class="container-x">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.videos.eyebrow')" :title="__('site.videos.title')">{{ __('site.videos.text') }}</x-section-heading>
                <a href="{{ route('videos') }}" wire:navigate class="btn-ghost reveal">{{ __('site.videos.all') }} <x-icon name="arrow" class="size-4" /></a>
            </div>
            <div class="mt-14">
                <livewire:video-gallery :limit="4" />
            </div>
        </div>
    </section>

    {{-- PROCESS --}}
    <section class="py-28 lg:py-36">
        <div class="container-x">
            <x-section-heading :eyebrow="__('site.process.eyebrow')" :title="__('site.process.title')" center />
            <ol class="mt-16 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                @foreach (__('site.process.steps') as [$stepTitle, $stepText])
                    <li class="reveal text-center" style="transition-delay: {{ $loop->index * 100 }}ms">
                        <span class="arch mx-auto grid h-24 w-20 place-items-center border border-gold-500/50 font-display text-3xl text-gold-500">{{ $loop->iteration }}</span>
                        <h3 class="mt-6 font-display text-3xl text-stone-50">{{ $stepTitle }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-stone-400">{{ $stepText }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- TESTIMONIALS --}}
    @if ($testimonials->isNotEmpty())
        <section class="bg-ink-900 py-28" x-data="{ t: 0, n: {{ $testimonials->count() }} }" x-init="setInterval(() => t = (t + 1) % n, 7000)">
            <div class="container-x">
                <x-section-heading :eyebrow="__('site.testimonials.eyebrow')" :title="__('site.testimonials.title')" center />
                <div class="reveal relative mx-auto mt-14 grid max-w-3xl">
                    @foreach ($testimonials as $testimonial)
                        <figure class="col-start-1 row-start-1 text-center transition duration-700"
                                :class="t === {{ $loop->index }} ? 'opacity-100 translate-y-0' : 'pointer-events-none opacity-0 translate-y-4'">
                            <div class="flex justify-center gap-1 text-gold-500">
                                @for ($s = 0; $s < $testimonial->rating; $s++)<x-icon name="star" class="size-4 fill-current" />@endfor
                            </div>
                            <blockquote class="mt-6 font-display text-3xl leading-snug text-stone-100 italic">“{{ $testimonial->tr('text') }}”</blockquote>
                            <figcaption class="mt-6 text-[11px] font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $testimonial->author }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- VISIT + REQUEST --}}
    <section id="request" class="relative overflow-hidden py-28 lg:py-36">
        <div class="pointer-events-none absolute top-1/3 -left-40 size-[34rem] rounded-full bg-gold-200/60 blur-3xl"></div>
        <div class="container-x relative grid gap-14 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <x-section-heading :eyebrow="__('site.form.eyebrow')" :title="__('site.form.title')">{{ __('site.form.text') }}</x-section-heading>
                <div class="reveal mt-10 space-y-5 border-t border-white/10 pt-8 text-stone-300">
                    <p class="flex items-center gap-4"><x-icon name="phone" class="size-5 text-gold-500" /><a href="tel:{{ preg_replace('/[^\d+]/', '', $settings['phone']) }}" class="font-display text-2xl text-stone-50 hover:text-gold-500">{{ $settings['phone'] }}</a></p>
                    <p class="flex items-center gap-4"><x-icon name="calendar" class="size-5 text-gold-500" />{{ __('site.visit.hours') }}: {{ $settings['hours'] }}, {{ __('site.visit.daily') }}</p>
                    <p class="flex items-center gap-4"><x-icon name="pin" class="size-5 text-gold-500" />{{ $settings['address'] }}</p>
                    @if ($instagram)
                        <a href="{{ $instagram }}" target="_blank" rel="noopener" class="flex items-center gap-4 hover:text-gold-500"><x-social network="instagram" class="size-5 text-gold-500" />@ {{ trim(parse_url($instagram, PHP_URL_PATH) ?? '', '/') }}</a>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-3">
                <livewire:inquiry-form />
            </div>
        </div>
    </section>
</x-layouts::app>
