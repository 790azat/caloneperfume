<div x-data="{ open: null }" @keydown.escape.window="open = null">
    <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
        @forelse ($videos as $video)
            <button type="button" wire:key="video-{{ $video->id }}"
                    @click="open = @js(['type' => $video->type, 'src' => $video->type === 'embed' ? $video->embedUrl() : $video->src(), 'poster' => $video->poster, 'href' => route('works.show', $video->work)])"
                    x-data="{ hover: false }" @mouseenter="hover = true; $refs.v?.play().catch(() => {})" @mouseleave="hover = false; $refs.v?.pause()"
                    class="reveal group relative aspect-[9/16] overflow-hidden rounded-[1.25rem] bg-[#16110d] text-left">
                @if ($video->type === 'video')
                    <video x-ref="v" src="{{ $video->src() }}" @if($video->poster) poster="{{ $video->poster }}" @endif muted loop playsinline preload="none"
                           class="absolute inset-0 size-full object-cover transition duration-1000 group-hover:scale-[1.03]"></video>
                @elseif ($video->thumbnail())
                    <img src="{{ $video->thumbnail() }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-1000 group-hover:scale-105">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#16110d]/80 via-transparent to-transparent transition" :class="hover && 'opacity-60'"></div>
                <span class="absolute top-1/2 left-1/2 grid size-14 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-[#fff]/50 bg-[#fff]/15 text-[#fff] backdrop-blur-md transition duration-500"
                      :class="hover && 'scale-75 opacity-0'">
                    <x-icon name="play" class="ml-0.5 size-5 fill-current" />
                </span>
                <div class="absolute inset-x-0 bottom-0 p-4">
                    <p class="font-display text-lg leading-tight text-[#f8f2e8]">{{ $video->caption ?: $video->work->name() }}</p>
                    @if ($video->work->event_date)<p class="mt-0.5 text-[11px] text-[#cbbda7]">{{ $video->work->event_date->translatedFormat('d F Y') }}</p>@endif
                </div>
            </button>
        @empty
            <p class="col-span-full py-12 text-center text-stone-500">{{ __('site.videos.empty') }}</p>
        @endforelse
    </div>

    @if ($total > $videos->count() && $limit > 4)
        <div class="mt-14 text-center">
            <button wire:click="loadMore" class="btn-ghost">{{ __('site.works.more') }}</button>
            <p class="mt-3 text-xs text-stone-500">{{ $videos->count() }} / {{ $total }}</p>
        </div>
    @endif

    <template x-teleport="body">
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[100] grid place-items-center bg-[#0d0a08]/95 p-4 backdrop-blur" @click.self="open = null">
            <button @click="open = null" class="absolute top-5 right-5 grid size-12 place-items-center rounded-full border border-[#fff]/20 text-[#fff] hover:border-[#c9a36a]" aria-label="Close">
                <x-icon name="x" />
            </button>
            <template x-if="open && open.type === 'video'">
                <video :src="open.src" :poster="open.poster" controls autoplay playsinline class="max-h-[88vh] max-w-full rounded-2xl bg-black"></video>
            </template>
            <template x-if="open && open.type === 'embed'">
                <div class="aspect-video w-full max-w-5xl overflow-hidden rounded-2xl bg-black">
                    <iframe :src="open.src + '&autoplay=1'" class="size-full" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                </div>
            </template>
        </div>
    </template>
</div>
