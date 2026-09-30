{{-- Three inputs (hy/ru/en) bound to e.g. `title.hy`, `title.ru`, `title.en`. --}}
@props(['model', 'label', 'textarea' => false, 'rows' => 4])
<div x-data="{ lang: 'ru' }">
    <div class="mb-1 flex items-center justify-between">
        <span class="a-label !mb-0">{{ $label }}</span>
        <div class="flex gap-1 text-xs">
            @foreach (config('app.locales') as $code => $name)
                <button type="button" @click="lang = '{{ $code }}'" class="rounded-md px-2 py-0.5"
                        :class="lang === '{{ $code }}' ? 'bg-gold-500 text-ink-950' : 'text-stone-400 hover:text-stone-200'">{{ strtoupper($code) }}</button>
            @endforeach
        </div>
    </div>
    @foreach (array_keys(config('app.locales')) as $code)
        <div x-show="lang === '{{ $code }}'" @if($code !== 'ru') x-cloak @endif>
            @if ($textarea)
                <textarea wire:model="{{ $model }}.{{ $code }}" rows="{{ $rows }}" class="a-field" lang="{{ $code }}" placeholder="{{ strtoupper($code) }}"></textarea>
            @else
                <input wire:model="{{ $model }}.{{ $code }}" class="a-field" lang="{{ $code }}" placeholder="{{ strtoupper($code) }}">
            @endif
        </div>
    @endforeach
    @error($model.'.ru')<p class="error">{{ $message }}</p>@enderror
</div>
