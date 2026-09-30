@props(['title', 'text'])
<section class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 pt-28 pb-16">
    <div class="pointer-events-none absolute -top-40 left-1/2 size-[40rem] -translate-x-1/2 rounded-full bg-gold-200/60 blur-3xl"></div>
    <div class="card relative w-full max-w-md p-8 text-center sm:p-10">
        <img src="/logo.svg" alt="" class="mx-auto mb-6 size-14">
        <h1 class="h-display text-4xl">{{ $title }}</h1>
        <p class="mt-2 text-sm text-stone-400">{{ $text }}</p>
        <div class="mt-8 text-left">{{ $slot }}</div>
    </div>
</section>
