<form wire:submit="save" class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        @if (session('saved'))
            <p class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('saved') }}</p>
        @endif

        <div class="a-card space-y-5">
            <x-admin.tr-input model="title" label="Название *" />
            <x-admin.tr-input model="description" label="Описание" textarea />
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="a-label">Категория</label>
                    <select wire:model="category_id" class="a-field">
                        <option value="">Без категории</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->tr('name', 'ru') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="a-label">Бренд / марка</label>
                    <input wire:model="location" class="a-field" placeholder="Ереван">
                </div>
                <div>
                    <label class="a-label">Дата</label>
                    <input wire:model="event_date" type="date" class="a-field [color-scheme:dark]">
                    @error('event_date')<p class="error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Uploads --}}
        <div class="a-card space-y-5">
            <h2 class="font-semibold text-stone-100">Добавить фото и видео</h2>

            @if (\App\Support\VercelBlob::enabled())
                <x-admin.blob-uploader folder="works" method="addBlob" />
                @if ($blobs)
                    <div>
                        <p class="a-label">Загружено, добавится после сохранения</p>
                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                            @foreach ($blobs as $i => $blob)
                                <div class="relative aspect-square overflow-hidden rounded-xl bg-ink-800" wire:key="blob-{{ $i }}">
                                    @if ($blob['type'] === 'image')
                                        <img src="{{ $blob['url'] }}" class="size-full object-cover" alt="">
                                    @else
                                        <video src="{{ $blob['url'] }}#t=1" muted preload="metadata" class="size-full object-cover"></video>
                                    @endif
                                    <button type="button" wire:click="removeBlob({{ $i }})" class="absolute top-1 right-1 rounded-full bg-black/70 p-1"><x-icon name="x" class="size-4" /></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['photos' => ['Фотографии', 'image/*', 'JPG, PNG, WEBP до 20 МБ', 'photo'], 'videos' => ['Видео', 'video/mp4,video/quicktime,video/webm', 'MP4, MOV, WEBM до 200 МБ', 'video']] as $field => [$label, $accept, $hint, $icon])
                    <label x-data="{ uploading: false, progress: 0 }"
                           x-on:livewire-upload-start="uploading = true"
                           x-on:livewire-upload-finish="uploading = false"
                           x-on:livewire-upload-cancel="uploading = false"
                           x-on:livewire-upload-error="uploading = false"
                           x-on:livewire-upload-progress="progress = $event.detail.progress"
                           class="relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-white/10 p-8 text-center transition hover:border-gold-500/60 hover:bg-gold-500/5">
                        <x-icon :name="$icon" class="size-10 text-gold-400" />
                        <span class="mt-3 font-medium text-stone-100">{{ $label }}</span>
                        <span class="mt-1 text-xs text-stone-500">{{ $hint }} · можно несколько сразу</span>
                        <input type="file" wire:model="{{ $field }}" accept="{{ $accept }}" multiple class="sr-only">
                        <div x-show="uploading" x-cloak class="absolute inset-x-6 bottom-4 h-1.5 overflow-hidden rounded-full bg-white/10">
                            <div class="h-full bg-gold-500 transition-all" :style="`width:${progress}%`"></div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('photos.*')<p class="error">{{ $message }}</p>@enderror
            @error('videos.*')<p class="error">{{ $message }}</p>@enderror

            @if ($photos || $videos)
                <div>
                    <p class="a-label">Будут загружены после сохранения</p>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @foreach ($photos as $i => $photo)
                            <div class="group relative aspect-square overflow-hidden rounded-xl bg-ink-800" wire:key="up-p-{{ $i }}">
                                @if ($photo->isPreviewable())<img src="{{ $photo->temporaryUrl() }}" class="size-full object-cover" alt="">@endif
                                <button type="button" wire:click="removeUpload('photos', {{ $i }})" class="absolute top-1 right-1 rounded-full bg-black/70 p-1"><x-icon name="x" class="size-4" /></button>
                            </div>
                        @endforeach
                        @foreach ($videos as $i => $video)
                            <div class="relative flex aspect-square flex-col items-center justify-center gap-1 overflow-hidden rounded-xl bg-ink-800 p-2 text-center text-xs text-stone-400" wire:key="up-v-{{ $i }}">
                                <x-icon name="video" class="size-8 text-gold-400" />
                                <span class="line-clamp-2 break-all">{{ $video->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeUpload('videos', {{ $i }})" class="absolute top-1 right-1 rounded-full bg-black/70 p-1"><x-icon name="x" class="size-4" /></button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @endif

            <div>
                <label class="a-label">…или ссылка на YouTube / Vimeo / видео / фото</label>
                <div class="flex items-center gap-2">
                    <x-icon name="link" class="size-5 shrink-0 text-stone-500" />
                    <input wire:model="link" class="a-field" placeholder="https://youtu.be/…">
                </div>
                @error('link')<p class="error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Existing media --}}
        @if ($work && $work->media->isNotEmpty())
            <div class="a-card">
                <h2 class="mb-1 font-semibold text-stone-100">Медиа этой работы ({{ $work->media->count() }})</h2>
                <p class="mb-4 text-xs text-stone-500">Первое фото используется как обложка. Порядок меняется стрелками.</p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($work->media as $media)
                        <div class="overflow-hidden rounded-xl border border-white/8 bg-ink-800" wire:key="m-{{ $media->id }}">
                            <div class="relative aspect-square">
                                @if ($media->type === 'video')
                                    <video src="{{ $media->src() }}#t=1" muted preload="metadata" class="size-full object-cover"></video>
                                    <span class="absolute top-2 left-2 rounded bg-black/70 px-1.5 text-[10px]">ВИДЕО</span>
                                @elseif ($media->thumbnail())
                                    <img src="{{ $media->thumbnail() }}" class="size-full object-cover" alt="" loading="lazy">
                                    @if ($media->type === 'embed')<span class="absolute top-2 left-2 rounded bg-black/70 px-1.5 text-[10px]">YOUTUBE</span>@endif
                                @else
                                    <div class="grid size-full place-items-center text-xs text-stone-500">{{ $media->type }}</div>
                                @endif
                                @if ($loop->first)<span class="absolute top-2 right-2 rounded bg-gold-500 px-1.5 text-[10px] font-bold text-ink-950">ОБЛОЖКА</span>@endif
                            </div>
                            <div class="p-2">
                                <input wire:model="captions.{{ $media->id }}" class="a-field !py-1 text-xs" placeholder="Подпись">
                                <div class="mt-2 flex items-center justify-between text-stone-400">
                                    <div class="flex">
                                        <button type="button" wire:click="move({{ $media->id }}, -1)" class="rounded p-1 hover:bg-white/5" title="Раньше"><x-icon name="arrow" class="size-4 rotate-180" /></button>
                                        <button type="button" wire:click="move({{ $media->id }}, 1)" class="rounded p-1 hover:bg-white/5" title="Позже"><x-icon name="arrow" class="size-4" /></button>
                                        @unless ($loop->first)
                                            <button type="button" wire:click="makeCover({{ $media->id }})" class="rounded p-1 hover:bg-white/5" title="Сделать обложкой"><x-icon name="star" class="size-4" /></button>
                                        @endunless
                                    </div>
                                    <button type="button" wire:click="deleteMedia({{ $media->id }})" wire:confirm="Удалить этот файл?" class="rounded p-1 text-rose-400 hover:bg-rose-500/10" title="Удалить"><x-icon name="trash" class="size-4" /></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <aside class="space-y-6">
        <div class="a-card sticky top-24 space-y-5">
            <label class="flex items-center justify-between text-sm">Показывать на сайте <x-admin.toggle :on="$is_published" wire:click="$toggle('is_published')" /></label>
            <label class="flex items-center justify-between text-sm">Избранная (на главной первой) <x-admin.toggle :on="$is_featured" wire:click="$toggle('is_featured')" /></label>
            <button type="submit" class="a-btn w-full justify-center bg-gold-500 py-3 text-ink-950 hover:bg-gold-300" wire:loading.attr="disabled" wire:target="save,photos,videos">
                <span wire:loading.remove wire:target="save">Сохранить</span>
                <span wire:loading wire:target="save">Сохраняем и загружаем…</span>
            </button>
            <div class="flex justify-between text-sm">
                <a href="{{ route('admin.works') }}" wire:navigate class="text-stone-400 hover:text-stone-100">← Все работы</a>
                @if ($work)<a href="{{ route('works.show', $work) }}" target="_blank" class="text-gold-300">Открыть на сайте</a>@endif
            </div>
        </div>
    </aside>
</form>
