<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <input wire:model.live.debounce.300ms="search" placeholder="Поиск по названию или бренду…" class="a-field max-w-sm">
        <a href="{{ route('admin.works.create') }}" wire:navigate class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300"><x-icon name="plus" class="size-4" /> Новая работа</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($works as $work)
            @php($cover = $work->cover())
            <div class="a-card flex flex-col !p-0 overflow-hidden" wire:key="w-{{ $work->id }}">
                <a href="{{ route('admin.works.edit', $work) }}" wire:navigate class="relative block aspect-video bg-ink-800">
                    @if ($cover?->type === 'video' && ! $cover->poster)
                        <video src="{{ $cover->src() }}#t=1" muted preload="metadata" class="size-full object-cover"></video>
                    @elseif ($cover?->thumbnail())
                        <img src="{{ $cover->thumbnail() }}" alt="" class="size-full object-cover" loading="lazy">
                    @endif
                    <span class="absolute top-3 left-3 flex gap-2 text-xs">
                        <span class="rounded-full bg-black/60 px-2 py-1">{{ $work->media->where('type', 'image')->count() }} фото</span>
                        <span class="rounded-full bg-black/60 px-2 py-1">{{ $work->media->where('type', '!=', 'image')->count() }} видео</span>
                    </span>
                </a>
                <div class="flex flex-1 flex-col p-4">
                    <p class="font-medium text-stone-100">{{ $work->tr('title', 'ru') ?: $work->name() }}</p>
                    <p class="text-xs text-stone-500">{{ $work->category?->tr('name', 'ru') ?? 'Без категории' }} · {{ $work->event_date?->format('d.m.Y') ?? '—' }} · {{ $work->views }} просм.</p>
                    <div class="mt-4 flex items-center justify-between gap-2 border-t border-white/5 pt-4 text-xs text-stone-400">
                        <label class="flex items-center gap-2">На сайте <x-admin.toggle :on="$work->is_published" wire:click="toggle({{ $work->id }}, 'is_published')" /></label>
                        <label class="flex items-center gap-2">Избранное <x-admin.toggle :on="$work->is_featured" wire:click="toggle({{ $work->id }}, 'is_featured')" /></label>
                        <div class="flex gap-1">
                            <a href="{{ route('works.show', $work) }}" target="_blank" class="rounded-lg p-1.5 hover:bg-white/5" title="Открыть"><x-icon name="eye" class="size-4" /></a>
                            <a href="{{ route('admin.works.edit', $work) }}" wire:navigate class="rounded-lg p-1.5 hover:bg-white/5" title="Редактировать"><x-icon name="pencil" class="size-4" /></a>
                            <button wire:click="delete({{ $work->id }})" wire:confirm="Удалить работу вместе со всеми фото и видео?" class="rounded-lg p-1.5 text-rose-400 hover:bg-rose-500/10" title="Удалить"><x-icon name="trash" class="size-4" /></button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="a-card col-span-full text-center text-stone-500">Работ пока нет. Нажмите «Новая работа», чтобы загрузить фото и видео.</p>
        @endforelse
    </div>

    {{ $works->links() }}
</div>
