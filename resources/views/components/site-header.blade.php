@props(['settings'])
@php
    $links = [
        'works' => route('works.index'),
        'videos' => route('videos'),
        'services' => route('home').'#boutique',
        'contact' => route('contact'),
    ];
@endphp
<header x-data="{ open: false, scrolled: false }"
        x-init="scrolled = window.scrollY > 30"
        @scroll.window="scrolled = window.scrollY > 30"
        :class="scrolled || open ? 'bg-ink-950/90 backdrop-blur-xl border-white/10' : 'bg-transparent border-transparent'"
        class="fixed inset-x-0 top-0 z-50 border-b transition-colors duration-500">
    <div class="container-x flex h-20 items-center justify-between gap-6">
        <a href="{{ route('home') }}" wire:navigate aria-label="Calone Perfume">
            <x-logo />
        </a>

        <nav class="hidden items-center gap-9 lg:flex">
            @foreach ($links as $key => $url)
                <a href="{{ $url }}" @if($key !== 'services') wire:navigate @endif
                   @class(['relative text-[11px] font-semibold tracking-[0.28em] uppercase transition hover:text-gold-500 after:absolute after:-bottom-1.5 after:left-1/2 after:h-px after:w-0 after:-translate-x-1/2 after:bg-gold-500 after:transition-all hover:after:w-full',
                       'text-gold-500 after:w-full' => $key === 'works' && request()->routeIs('works.*') || $key === 'videos' && request()->routeIs('videos') || $key === 'contact' && request()->routeIs('contact'),
                       'text-stone-200' => true])>
                    {{ __('site.nav.'.$key) }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2 sm:gap-4">
            <x-locale-switcher />

            @auth
                <div x-data="{ menu: false }" class="relative hidden sm:block">
                    <button @click="menu = !menu" @click.outside="menu = false" class="grid size-10 place-items-center rounded-full border border-white/15 text-stone-300 transition hover:border-gold-500 hover:text-gold-500" aria-label="{{ auth()->user()->name }}">
                        <x-icon name="user" class="size-4.5" />
                    </button>
                    <div x-cloak x-show="menu" x-transition class="absolute right-0 mt-3 w-56 overflow-hidden rounded-2xl border border-white/10 bg-ink-900 py-2 shadow-2xl">
                        <p class="truncate px-4 py-2 text-xs text-stone-500">{{ auth()->user()->email }}</p>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gold-500 hover:bg-ink-800">{{ __('site.nav.admin') }}</a>
                        @endif
                        <a href="{{ route('account') }}" wire:navigate class="block px-4 py-2 text-sm hover:bg-ink-800">{{ __('site.nav.account') }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="w-full px-4 py-2 text-left text-sm text-stone-400 hover:bg-ink-800">{{ __('site.nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" wire:navigate class="hidden text-[11px] font-semibold tracking-[0.28em] text-stone-300 uppercase hover:text-gold-500 sm:block">{{ __('site.nav.login') }}</a>
            @endauth

            <a href="{{ route('contact') }}" wire:navigate class="btn-gold hidden !px-5 !py-3 xl:inline-flex">{{ __('site.nav.book') }}</a>

            <button @click="open = !open" class="grid size-10 place-items-center rounded-full border border-white/15 lg:hidden" aria-label="Menu">
                <x-icon name="menu" x-show="!open" />
                <x-icon name="x" x-cloak x-show="open" />
            </button>
        </div>
    </div>

    <div x-cloak x-show="open" x-collapse class="border-t border-white/10 lg:hidden">
        <nav class="container-x flex flex-col gap-1 py-6">
            @foreach (['home' => route('home')] + $links as $key => $url)
                <a href="{{ $url }}" @click="open = false" class="py-2 font-display text-3xl text-stone-50 hover:text-gold-500">{{ __('site.nav.'.$key) }}</a>
            @endforeach
            <div class="mt-5 flex flex-wrap gap-3 border-t border-white/10 pt-6">
                @auth
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="btn-ghost">{{ __('site.nav.admin') }}</a>
                    @endif
                    <a href="{{ route('account') }}" class="btn-ghost">{{ __('site.nav.account') }}</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-ghost">{{ __('site.nav.logout') }}</button></form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">{{ __('site.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="btn-ghost">{{ __('site.nav.register') }}</a>
                @endauth
                <a href="{{ route('contact') }}" class="btn-gold">{{ __('site.nav.book') }}</a>
            </div>
        </nav>
    </div>
</header>
