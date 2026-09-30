<div class="flex items-center gap-0.5 text-[11px] font-semibold tracking-[0.15em] uppercase">
    @foreach (config('app.locales') as $code => $label)
        <a href="{{ route('locale', $code) }}"
           @class([
               'rounded-full px-2 py-1 transition',
               'text-gold-500' => app()->getLocale() === $code,
               'text-stone-400 hover:text-stone-50' => app()->getLocale() !== $code,
           ])
           hreflang="{{ $code }}" lang="{{ $code }}">{{ $label }}</a>
        @if (! $loop->last)<span class="text-stone-500/50">/</span>@endif
    @endforeach
</div>
