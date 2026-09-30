<div class="max-w-4xl space-y-6">
    <button wire:click="edit" class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300"><x-icon name="plus" class="size-4" /> Новая услуга</button>

    @if ($editing !== null)
        <form wire:submit="save" class="a-card space-y-4">
            <div>
                <span class="a-label">Иконка</span>
                <div class="flex flex-wrap gap-2">
                    @foreach ($icons as $name)
                        <button type="button" wire:click="$set('icon', '{{ $name }}')" @class(['grid size-11 place-items-center rounded-xl border', 'border-gold-500 bg-gold-500/15 text-gold-300' => $icon === $name, 'border-white/10 text-stone-400' => $icon !== $name])><x-icon :name="$name" /></button>
                    @endforeach
                </div>
            </div>
            <x-admin.tr-input model="title" label="Название (RU обязательно)" />
            <x-admin.tr-input model="description" label="Описание" textarea rows="3" />
            <div class="flex gap-2">
                <button class="a-btn bg-gold-500 text-ink-950">Сохранить</button>
                <button type="button" wire:click="$set('editing', null)" class="a-btn text-stone-400">Отмена</button>
            </div>
        </form>
    @endif

    <div class="a-card divide-y divide-white/5 !py-2">
        @forelse ($services as $service)
            <div class="flex items-center gap-4 py-3" wire:key="s-{{ $service->id }}">
                <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gold-500/15 text-gold-400"><x-icon :name="$service->icon" /></span>
                <div class="min-w-0 flex-1">
                    <p class="text-stone-100">{{ $service->tr('title', 'ru') }}</p>
                    <p class="truncate text-xs text-stone-500">{{ $service->tr('description', 'ru') }}</p>
                </div>
                <x-admin.toggle :on="$service->is_active" wire:click="toggle({{ $service->id }})" title="Показывать" />
                <div class="flex">
                    <button wire:click="move({{ $service->id }}, -1)" class="rounded-lg p-2 hover:bg-white/5"><x-icon name="arrow" class="size-4 -rotate-90" /></button>
                    <button wire:click="move({{ $service->id }}, 1)" class="rounded-lg p-2 hover:bg-white/5"><x-icon name="arrow" class="size-4 rotate-90" /></button>
                    <button wire:click="edit({{ $service->id }})" class="rounded-lg p-2 hover:bg-white/5"><x-icon name="pencil" class="size-4" /></button>
                    <button wire:click="delete({{ $service->id }})" wire:confirm="Удалить услугу?" class="rounded-lg p-2 text-rose-400 hover:bg-rose-500/10"><x-icon name="trash" class="size-4" /></button>
                </div>
            </div>
        @empty
            <p class="py-6 text-center text-stone-500">Услуг нет.</p>
        @endforelse
    </div>
</div>
