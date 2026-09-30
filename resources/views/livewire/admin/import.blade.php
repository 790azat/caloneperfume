<div class="max-w-4xl space-y-6">
    <div class="a-card space-y-4">
        <div>
            <h2 class="font-semibold text-stone-100">Рабочая папка</h2>
            <p class="mt-1 text-sm text-stone-500">Папка с фото и видео (например, выгрузка из Instagram). Каждый пост становится отдельной работой в портфолио: файлы вида <code class="text-stone-300">123_456.jpg</code> и <code class="text-stone-300">123_789.jpg</code> попадут в одну карусель. Уже импортированное повторно не добавляется, даже если работу удалили.</p>
        </div>
        <form wire:submit="saveFolder" class="flex flex-wrap gap-3">
            <input wire:model="folder" class="a-field flex-1" placeholder="C:\Users\...\Instagram\caloneperfume">
            <button class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300">Сохранить и проверить</button>
        </form>

        @if ($readable && $summary)
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([['Постов', $summary['posts']], ['Фото', $summary['images']], ['Видео', $summary['videos']], ['Новых', $summary['new']]] as [$label, $value])
                    <div class="rounded-xl bg-ink-800 p-4"><p class="text-2xl font-semibold text-stone-50">{{ $value }}</p><p class="text-xs text-stone-500">{{ $label }}</p></div>
                @endforeach
            </div>
            @if ($summary['new'] > 0)
                <div x-data="{ running: false, left: {{ $summary['new'] }} }">
                    <button type="button" class="a-btn bg-gold-500 text-ink-950 hover:bg-gold-300" :disabled="running"
                            @click="running = true; while (left > 0) { left = await $wire.importBatch(10) } running = false; $dispatch('toast', { message: 'Импорт завершён' })">
                        <x-icon name="upload" class="size-4" />
                        <span x-text="running ? `Импорт… осталось ${left}` : 'Импортировать новые ({{ $summary['new'] }})'"></span>
                    </button>
                </div>
            @else
                <p class="text-sm text-emerald-400">Всё из этой папки уже на сайте.</p>
            @endif
        @elseif ($folder)
            <p class="text-sm text-stone-400">Сервер не видит эту папку: на Vercel сайт не имеет доступа к вашему компьютеру. Путь сохранён, а файлы можно загрузить из браузера ниже.</p>
        @endif

        @if ($imported)
            <p class="text-sm text-gold-300">Добавлено работ за этот сеанс: {{ $imported }}. <a href="{{ route('admin.works') }}" wire:navigate class="underline">Открыть список</a></p>
        @endif
    </div>

    <div class="a-card space-y-4" x-data="folderImporter()">
        <div>
            <h2 class="font-semibold text-stone-100">Загрузить папку с компьютера</h2>
            <p class="mt-1 text-sm text-stone-500">Выберите рабочую папку: сайт найдёт новые посты и загрузит только их. Вкладку не закрывайте, пока идёт загрузка.</p>
        </div>
        @if ($blob)
            <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-white/10 p-8 text-center transition hover:border-gold-500/60 hover:bg-gold-500/5" :class="busy && 'pointer-events-none opacity-60'">
                <x-icon name="upload" class="size-10 text-gold-400" />
                <span class="mt-3 font-medium text-stone-100">Выбрать папку</span>
                <span class="mt-1 text-xs text-stone-500">JPG, PNG, WEBP, MP4, MOV · до 500 МБ на файл</span>
                <input type="file" webkitdirectory directory multiple class="sr-only" @change="pick($event)">
            </label>
            <template x-if="total">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-stone-400">
                        <span x-text="status"></span>
                        <span x-text="`${done} / ${total}`"></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-white/10"><div class="h-full bg-gold-500 transition-all" :style="`width:${total ? done / total * 100 : 0}%`"></div></div>
                </div>
            </template>
            <p x-show="error" x-text="error" class="error" x-cloak></p>
        @else
            <p class="text-sm text-stone-400">Загрузка из браузера включится, когда к проекту на Vercel будет подключено хранилище Blob.</p>
        @endif
    </div>
</div>
