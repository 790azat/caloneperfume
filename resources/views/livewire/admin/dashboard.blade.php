<div class="space-y-8" wire:poll.60s>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
        @foreach ($stats as [$label, $value, $icon, $url])
            <a href="{{ $url }}" wire:navigate class="a-card transition hover:border-gold-500/40">
                <x-icon :name="$icon" class="size-6 text-gold-400" />
                <p class="mt-4 text-3xl font-semibold text-stone-50">{{ number_format($value, 0, '.', ' ') }}</p>
                <p class="text-xs text-stone-500">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.works.create') }}" wire:navigate class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300"><x-icon name="upload" class="size-4" /> Загрузить новую работу</a>
        <a href="{{ route('admin.settings') }}" wire:navigate class="a-btn border border-white/10 hover:border-gold-500">Контакты и соцсети</a>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="a-card xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold text-stone-100">Последние заявки</h2>
                <a href="{{ route('admin.inquiries') }}" wire:navigate class="text-sm text-gold-300">Все заявки</a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse ($inquiries as $inquiry)
                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                        <div>
                            <p class="font-medium text-stone-100">{{ $inquiry->name }} · <a href="tel:{{ $inquiry->phone }}" class="text-gold-300">{{ $inquiry->phone }}</a></p>
                            <p class="text-stone-500">{{ __('site.form.types.'.($inquiry->event_type ?: 'other'), [], 'ru') }} · {{ $inquiry->event_date?->format('d.m.Y') ?? 'без даты' }} · {{ $inquiry->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-white/5 px-3 py-1 text-xs">{{ __('site.status.'.$inquiry->status, [], 'ru') }}</span>
                    </div>
                @empty
                    <p class="py-6 text-center text-stone-500">Заявок пока нет.</p>
                @endforelse
            </div>
        </div>
        <div class="a-card">
            <h2 class="mb-4 font-semibold text-stone-100">Популярные работы</h2>
            <ol class="space-y-3 text-sm">
                @forelse ($popular as $work)
                    <li class="flex justify-between gap-3"><a href="{{ route('admin.works.edit', $work) }}" wire:navigate class="truncate hover:text-gold-300">{{ $work->tr('title', 'ru') ?: $work->name() }}</a><span class="text-stone-500">{{ $work->views }}</span></li>
                @empty
                    <li class="text-stone-500">Пока пусто.</li>
                @endforelse
            </ol>
        </div>
    </div>
</div>
