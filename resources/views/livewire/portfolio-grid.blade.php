<div>
    @if ($categories->count() > 1)
        <div class="reveal mb-12 flex flex-wrap justify-center gap-2">
            <button wire:click="filter('')" @class(['rounded-full border px-5 py-2 text-[11px] font-semibold tracking-[0.2em] uppercase transition', 'border-stone-50 bg-stone-50 text-ink-950' => $category === '', 'border-white/15 text-stone-300 hover:border-gold-500 hover:text-gold-500' => $category !== ''])>{{ __('site.works.all') }}</button>
            @foreach ($categories as $cat)
                <button wire:click="filter('{{ $cat->slug }}')" @class(['rounded-full border px-5 py-2 text-[11px] font-semibold tracking-[0.2em] uppercase transition', 'border-stone-50 bg-stone-50 text-ink-950' => $category === $cat->slug, 'border-white/15 text-stone-300 hover:border-gold-500 hover:text-gold-500' => $category !== $cat->slug])>{{ $cat->tr('name') }}</button>
            @endforeach
        </div>
    @endif

    <div wire:loading.class="opacity-50" class="grid grid-cols-2 gap-3 transition sm:gap-5 lg:grid-cols-4">
        @forelse ($works as $work)
            @php($cover = $work->cover())
            @php($photos = $work->media->where('type', 'image')->count())
            @php($clips = $work->media->where('type', '!=', 'image')->count())
            <a href="{{ route('works.show', $work) }}" wire:navigate wire:key="work-{{ $work->id }}"
               class="reveal group relative block overflow-hidden rounded-[1.25rem] bg-ink-800">
                <div class="relative aspect-[4/5] w-full overflow-hidden">
                    @if ($cover?->thumbnail())
                        <img src="{{ $cover->thumbnail() }}" alt="{{ $work->name() }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition duration-[1400ms] ease-out group-hover:scale-[1.06]">
                    @elseif ($cover?->type === 'video')
                        <video src="{{ $cover->src() }}#t=1" muted playsinline preload="metadata" class="absolute inset-0 size-full object-cover"></video>
                    @else
                        <div class="absolute inset-0 grid place-items-center text-gold-500/40"><img src="/logo.svg" alt="" class="size-16 opacity-40"></div>
                    @endif

                    @if ($clips)
                        <span class="absolute top-3 right-3 grid size-8 place-items-center rounded-full bg-[#16110d]/60 text-[#fff] backdrop-blur"><x-icon name="play" class="ml-0.5 size-3.5 fill-current" /></span>
                    @elseif ($photos > 1)
                        <span class="absolute top-3 right-3 rounded-full bg-[#16110d]/60 px-2.5 py-1 text-[10px] font-semibold text-[#fff] backdrop-blur">1/{{ $photos }}</span>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-[#16110d]/85 via-[#16110d]/0 to-transparent opacity-0 transition duration-500 group-hover:opacity-100"></div>
                    <div class="absolute inset-x-0 bottom-0 translate-y-3 p-4 opacity-0 transition duration-500 group-hover:translate-y-0 group-hover:opacity-100 sm:p-5">
                        <p class="text-[10px] font-semibold tracking-[0.3em] text-[#e3c793] uppercase">{{ $work->category?->tr('name') }}</p>
                        <h3 class="mt-1 font-display text-xl leading-tight text-[#f8f2e8] sm:text-2xl">{{ $work->name() }}</h3>
                        @if ($work->event_date)<p class="mt-1 text-[11px] text-[#cbbda7]">{{ $work->event_date->translatedFormat('d F Y') }}</p>@endif
                    </div>
                </div>
            </a>
        @empty
            <p class="col-span-full py-16 text-center text-stone-500">{{ __('site.works.empty') }}</p>
        @endforelse
    </div>

    @if ($total > $works->count())
        <div class="mt-14 text-center">
            <button wire:click="loadMore" wire:loading.attr="disabled" class="btn-ghost">
                <span wire:loading.remove wire:target="loadMore">{{ __('site.works.more') }}</span>
                <span wire:loading wire:target="loadMore">…</span>
            </button>
            <p class="mt-3 text-xs text-stone-500">{{ $works->count() }} / {{ $total }}</p>
        </div>
    @endif
</div>
