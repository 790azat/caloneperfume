@props(['size' => 'size-10', 'light' => false])
<span {{ $attributes->class('group inline-flex items-center gap-3') }}>
    <img src="/logo.svg" alt="" class="{{ $size }} shrink-0 transition duration-700 group-hover:rotate-[20deg]" width="40" height="40">
    <span class="leading-none">
        <span @class(['block font-display text-[1.25rem] sm:text-[1.55rem] font-semibold tracking-[0.18em]', 'text-stone-50' => ! $light, 'text-[#f8f2e8]' => $light])>CALONE</span>
        <span class="mt-0.5 block text-[8.5px] font-semibold tracking-[0.62em] text-gold-500 uppercase">Perfume</span>
    </span>
</span>
