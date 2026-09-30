<div class="max-w-3xl space-y-6">
    <button wire:click="edit" class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300"><x-icon name="plus" class="size-4" /> Новая категория</button>

    @if ($editing !== null)
        <form wire:submit="save" class="a-card space-y-4">
            <x-admin.tr-input model="name" label="Название категории (RU обязательно)" />
            <div class="flex gap-2">
                <button class="a-btn bg-gold-500 text-ink-950">Сохранить</button>
                <button type="button" wire:click="$set('editing', null)" class="a-btn text-stone-400">Отмена</button>
            </div>
        </form>
    @endif

    <div class="a-card divide-y divide-white/5 !py-2">
        @forelse ($categories as $category)
            <div class="flex items-center justify-between py-3" wire:key="c-{{ $category->id }}">
                <div>
                    <p class="text-stone-100">{{ $category->tr('name', 'ru') }}</p>
                    <p class="text-xs text-stone-500">{{ $category->tr('name', 'hy') }} · {{ $category->tr('name', 'en') }} · работ: {{ $category->works_count }}</p>
                </div>
                <div class="flex gap-1">
                    <button wire:click="edit({{ $category->id }})" class="rounded-lg p-2 hover:bg-white/5"><x-icon name="pencil" class="size-4" /></button>
                    <button wire:click="delete({{ $category->id }})" wire:confirm="Удалить категорию? Работы останутся без категории." class="rounded-lg p-2 text-rose-400 hover:bg-rose-500/10"><x-icon name="trash" class="size-4" /></button>
                </div>
            </div>
        @empty
            <p class="py-6 text-center text-stone-500">Категорий нет.</p>
        @endforelse
    </div>
</div>
