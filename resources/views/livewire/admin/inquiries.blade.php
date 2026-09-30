<div class="space-y-6" wire:poll.30s>
    <div class="flex flex-wrap gap-2">
        @foreach (['' => 'Все', 'new' => 'Новые', 'in_progress' => 'В работе', 'done' => 'Выполнены'] as $value => $label)
            <button wire:click="$set('status', '{{ $value }}')" @class(['rounded-full border px-4 py-1.5 text-sm', 'border-gold-500 bg-gold-500 text-ink-950' => $status === $value, 'border-white/10 text-stone-400' => $status !== $value])>
                {{ $label }} @if ($value)<span class="opacity-70">{{ $counts[$value] ?? 0 }}</span>@endif
            </button>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($inquiries as $inquiry)
            <div class="a-card" wire:key="i-{{ $inquiry->id }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="font-medium text-stone-100">{{ $inquiry->name }}
                            <span class="ml-2 text-xs text-stone-500">{{ $inquiry->created_at->format('d.m.Y H:i') }}</span>
                            @if ($inquiry->user)<span class="ml-2 rounded bg-white/5 px-1.5 text-[10px] text-stone-400">аккаунт</span>@endif
                        </p>
                        <p class="mt-1 flex flex-wrap gap-x-4 text-sm">
                            <a href="tel:{{ $inquiry->phone }}" class="text-gold-300">{{ $inquiry->phone }}</a>
                            @if ($inquiry->email)<a href="mailto:{{ $inquiry->email }}" class="text-stone-400">{{ $inquiry->email }}</a>@endif
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $inquiry->phone) }}" target="_blank" class="text-emerald-400">WhatsApp</a>
                        </p>
                        <p class="mt-2 text-sm text-stone-400">
                            {{ __('site.form.types.'.($inquiry->event_type ?: 'other'), [], 'ru') }}
                            · {{ $inquiry->event_date?->format('d.m.Y') ?? 'дата не указана' }}
                            @if ($inquiry->guests) · {{ $inquiry->guests }} гостей @endif
                        </p>
                        @if ($inquiry->message)<p class="mt-2 max-w-3xl text-sm whitespace-pre-line text-stone-300">{{ $inquiry->message }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2">
                        <select wire:change="setStatus({{ $inquiry->id }}, $event.target.value)" class="a-field !w-auto">
                            @foreach (['new' => 'Новая', 'in_progress' => 'В работе', 'done' => 'Выполнена'] as $value => $label)
                                <option value="{{ $value }}" @selected($inquiry->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button wire:click="delete({{ $inquiry->id }})" wire:confirm="Удалить заявку?" class="rounded-lg p-2 text-rose-400 hover:bg-rose-500/10"><x-icon name="trash" class="size-4" /></button>
                    </div>
                </div>
            </div>
        @empty
            <p class="a-card text-center text-stone-500">Заявок нет.</p>
        @endforelse
    </div>

    {{ $inquiries->links() }}
</div>
