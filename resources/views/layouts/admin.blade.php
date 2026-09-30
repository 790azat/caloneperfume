@php
    $nav = [
        ['admin.dashboard', 'home', 'Обзор'],
        ['admin.works', 'photo', 'Работы и видео'],
        ['admin.import', 'upload', 'Импорт из папки'],
        ['admin.categories', 'squares', 'Категории'],
        ['admin.services', 'sparkles', 'Услуги'],
        ['admin.testimonials', 'chat', 'Отзывы'],
        ['admin.inquiries', 'inbox', 'Заявки'],
        ['admin.users', 'users', 'Пользователи'],
        ['admin.settings', 'cog', 'Настройки'],
    ];
    $newInquiries = \App\Models\Inquiry::where('status', 'new')->count();
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Админ-панель' }} · Calone Perfume</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600;700&family=Noto+Sans+Armenian:wght@400;500;600;700&family=Noto+Serif+Armenian:wght@500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>
<body class="min-h-screen font-sans" x-data="{ side: false }">
    <aside :class="side ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-white/8 bg-ink-900 transition-transform">
        <a href="{{ route('home') }}" class="flex h-16 items-center gap-3 border-b border-white/8 px-5">
            <x-logo size="size-9" light />
            <span class="ml-auto rounded-full bg-white/8 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-stone-400">Admin</span>
        </a>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($nav as [$route, $icon, $label])
                <a href="{{ route($route) }}" wire:navigate
                   @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition',
                       'bg-gold-500/15 text-gold-300' => request()->routeIs($route.'*'),
                       'text-stone-400 hover:bg-white/5 hover:text-stone-100' => ! request()->routeIs($route.'*')])>
                    <x-icon :name="$icon" /> {{ $label }}
                    @if ($route === 'admin.inquiries' && $newInquiries)
                        <span class="ml-auto rounded-full bg-gold-500 px-2 text-xs font-bold text-ink-950">{{ $newInquiries }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="border-t border-white/8 p-3">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-400 hover:bg-white/5"><x-icon name="globe" /> Открыть сайт</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-400 hover:bg-white/5"><x-icon name="arrow" class="size-5 rotate-180" /> Выйти</button>
            </form>
        </div>
    </aside>
    <div x-cloak x-show="side" @click="side = false" class="fixed inset-0 z-30 bg-black/60 lg:hidden"></div>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-white/8 bg-ink-950/80 px-5 backdrop-blur">
            <div class="flex items-center gap-3">
                <button @click="side = true" class="lg:hidden" aria-label="Menu"><x-icon name="menu" /></button>
                <h1 class="font-display text-2xl text-stone-50">{{ $title ?? 'Админ-панель' }}</h1>
            </div>
            <span class="text-sm text-stone-500">{{ auth()->user()->name }}</span>
        </header>
        <main class="p-5 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    <div x-data="{ msg: null }" @toast.window="msg = $event.detail.message ?? $event.detail[0]?.message ?? $event.detail; setTimeout(() => msg = null, 3000)"
         x-cloak x-show="msg" x-transition class="fixed right-5 bottom-5 z-50 rounded-xl border border-gold-500/30 bg-ink-800 px-5 py-3 text-sm text-gold-200 shadow-2xl" x-text="msg"></div>
</body>
</html>
