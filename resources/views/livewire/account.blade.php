<section class="container-x min-h-screen pt-32 pb-24">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">{{ auth()->user()->name }}</p>
            <h1 class="h-display mt-3 text-5xl">{{ __('site.account.title') }}</h1>
        </div>
        <a href="{{ route('contact') }}" wire:navigate class="btn-gold"><x-icon name="plus" class="size-4" /> {{ __('site.account.new') }}</a>
    </div>

    <div class="mt-12 grid gap-4" wire:poll.30s>
        @forelse ($inquiries as $inquiry)
            <article class="card flex flex-wrap items-center justify-between gap-4 p-6">
                <div>
                    <p class="font-display text-2xl text-stone-50">{{ __('site.form.types.'.($inquiry->event_type ?: 'other')) }}</p>
                    <p class="mt-1 text-sm text-stone-400">
                        {{ $inquiry->event_date?->translatedFormat('d F Y') ?? '—' }}
                    </p>
                    @if ($inquiry->message)<p class="mt-2 max-w-2xl text-sm text-stone-500">{{ Str::limit($inquiry->message, 160) }}</p>@endif
                </div>
                <span @class(['rounded-full px-4 py-1.5 text-xs font-semibold',
                    'bg-sky-500/15 text-sky-300' => $inquiry->status === 'new',
                    'bg-amber-500/15 text-amber-300' => $inquiry->status === 'in_progress',
                    'bg-emerald-500/15 text-emerald-300' => $inquiry->status === 'done'])>{{ __('site.status.'.$inquiry->status) }}</span>
            </article>
        @empty
            <p class="card p-10 text-center text-stone-400">{{ __('site.account.empty') }}</p>
        @endforelse
    </div>
</section>
