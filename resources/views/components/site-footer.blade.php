@props(['settings'])
<footer class="theme-noir grain relative overflow-hidden bg-ink-950 pt-24 pb-10 text-stone-300">
    <div class="pointer-events-none absolute -top-48 left-1/2 size-[36rem] -translate-x-1/2 rounded-full bg-gold-500/10 blur-3xl"></div>
    <div class="container-x relative">
        <div class="flex flex-col items-center text-center">
            <x-logo size="size-14" light />
            <p class="mt-8 max-w-xl font-display text-3xl leading-snug text-stone-100 italic sm:text-4xl">{{ __('site.footer.made') }}</p>
            <div class="mt-8 flex gap-3">
                @foreach (['instagram', 'facebook', 'whatsapp', 'telegram'] as $network)
                    @if ($settings[$network])
                        @php($href = match ($network) {
                            'whatsapp' => 'https://wa.me/'.preg_replace('/\D/', '', $settings[$network]),
                            'telegram' => str_starts_with($settings[$network], 'http') ? $settings[$network] : 'https://t.me/'.ltrim($settings[$network], '@'),
                            default => $settings[$network],
                        })
                        <a href="{{ $href }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"
                           class="grid size-11 place-items-center rounded-full border border-white/15 text-stone-300 transition hover:border-gold-500 hover:bg-gold-500 hover:text-ink-950">
                            <x-social :network="$network" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="hairline mt-16"></div>

        <div class="mt-12 grid gap-10 text-sm sm:grid-cols-3">
            <div>
                <p class="eyebrow mb-4">{{ __('site.contact.title') }}</p>
                <ul class="space-y-3">
                    <li class="flex gap-3"><x-icon name="phone" class="size-4.5 text-gold-500" /><a href="tel:{{ preg_replace('/[^\d+]/', '', $settings['phone']) }}" class="hover:text-gold-300">{{ $settings['phone'] }}</a></li>
                    <li class="flex gap-3"><x-icon name="mail" class="size-4.5 text-gold-500" /><a href="mailto:{{ $settings['email'] }}" class="hover:text-gold-300">{{ $settings['email'] }}</a></li>
                    <li class="flex gap-3"><x-icon name="pin" class="size-4.5 text-gold-500" />{{ $settings['address'] }}</li>
                </ul>
            </div>
            <div>
                <p class="eyebrow mb-4">{{ __('site.visit.hours') }}</p>
                <p class="font-display text-3xl text-stone-100">{{ $settings['hours'] }}</p>
                <p class="mt-1 text-stone-400">{{ __('site.visit.daily') }}</p>
            </div>
            <div>
                <p class="eyebrow mb-4">Calone</p>
                <ul class="space-y-2">
                    <li><a href="{{ route('works.index') }}" wire:navigate class="hover:text-gold-300">{{ __('site.nav.works') }}</a></li>
                    <li><a href="{{ route('works.index', ['category' => 'by-calone']) }}" wire:navigate class="hover:text-gold-300">BY CALONE</a></li>
                    <li><a href="{{ route('videos') }}" wire:navigate class="hover:text-gold-300">{{ __('site.nav.videos') }}</a></li>
                    <li><a href="{{ route('contact') }}" wire:navigate class="hover:text-gold-300">{{ __('site.nav.contact') }}</a></li>
                    @guest<li><a href="{{ route('register') }}" wire:navigate class="hover:text-gold-300">{{ __('site.nav.register') }}</a></li>@endguest
                </ul>
            </div>
        </div>

        <div class="mt-16 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-8 text-xs text-stone-500 sm:flex-row">
            <p>© {{ date('Y') }} Calone Perfume. {{ __('site.footer.rights') }}</p>
            <p class="tracking-[0.3em] uppercase">Selective perfumes · Yerevan</p>
        </div>
    </div>
</footer>
