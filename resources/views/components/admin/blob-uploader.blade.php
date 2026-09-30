{{-- Drop zone that uploads straight to Vercel Blob; calls `$wire.{method}(kind, url)` for each finished file. --}}
@props(['folder', 'method', 'accept' => 'image/*,video/*', 'label' => 'Фото и видео', 'hint' => 'Фото и видео до 500 МБ · можно несколько сразу'])
<div x-data="blobUploader({ folder: '{{ $folder }}', onUploaded: (kind, url) => $wire.{{ $method }}(kind, url) })">
    <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-white/10 p-8 text-center transition hover:border-gold-500/60 hover:bg-gold-500/5">
        <x-icon name="upload" class="size-10 text-gold-400" />
        <span class="mt-3 font-medium text-stone-100">{{ $label }}</span>
        <span class="mt-1 text-xs text-stone-500">{{ $hint }}</span>
        <input type="file" accept="{{ $accept }}" multiple class="sr-only" @change="pick($event)">
    </label>
    <template x-for="(item, i) in queue" :key="i">
        <div class="mt-2 text-xs">
            <div class="flex justify-between text-stone-400"><span class="truncate" x-text="item.name"></span><span x-text="item.failed ? 'ошибка' : item.progress + '%'"></span></div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full transition-all" :class="item.failed ? 'bg-rose-500' : 'bg-gold-500'" :style="`width:${item.failed ? 100 : item.progress}%`"></div></div>
        </div>
    </template>
    <p x-show="error" x-text="error" class="error" x-cloak></p>
</div>
