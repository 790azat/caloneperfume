<div class="max-w-4xl space-y-6">
    <button wire:click="edit" class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300"><x-icon name="plus" class="size-4" /> Новый отзыв</button>

    @if ($editing !== null)
        <form wire:submit="save" class="a-card space-y-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <div><label class="a-label">Автор *</label><input wire:model="author" class="a-field">@error('author')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="a-label">Событие</label><input wire:model="event" class="a-field" placeholder="Свадьба, 2025"></div>
                <div><label class="a-label">Оценка</label>
                    <select wire:model="rating" class="a-field">@for ($r = 5; $r >= 1; $r--)<option value="{{ $r }}">{{ str_repeat('★', $r) }}</option>@endfor</select></div>
            </div>
            <x-admin.tr-input model="text" label="Текст отзыва" textarea rows="3" />
            <div class="flex gap-2">
                <button class="a-btn bg-gold-500 text-ink-950">Сохранить</button>
                <button type="button" wire:click="$set('editing', null)" class="a-btn text-stone-400">Отмена</button>
            </div>
        </form>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($items as $item)
            <div class="a-card" wire:key="t-{{ $item->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-stone-100">{{ $item->author }} <span class="text-gold-400">{{ str_repeat('★', $item->rating) }}</span></p>
                        <p class="text-xs text-stone-500">{{ $item->event }}</p>
                    </div>
                    <x-admin.toggle :on="$item->is_active" wire:click="toggle({{ $item->id }})" />
                </div>
                <p class="mt-3 text-sm text-stone-400">{{ Str::limit($item->tr('text', 'ru'), 200) }}</p>
                <div class="mt-3 flex justify-end gap-1">
                    <button wire:click="edit({{ $item->id }})" class="rounded-lg p-2 hover:bg-white/5"><x-icon name="pencil" class="size-4" /></button>
                    <button wire:click="delete({{ $item->id }})" wire:confirm="Удалить отзыв?" class="rounded-lg p-2 text-rose-400 hover:bg-rose-500/10"><x-icon name="trash" class="size-4" /></button>
                </div>
            </div>
        @empty
            <p class="a-card col-span-full text-center text-stone-500">Отзывов нет.</p>
        @endforelse
    </div>
</div>
