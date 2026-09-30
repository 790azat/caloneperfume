@props(['on' => false])
<button type="button" {{ $attributes }} @class(['relative h-6 w-11 rounded-full transition', 'bg-gold-500' => $on, 'bg-white/15' => ! $on])>
    <span @class(['absolute top-0.5 size-5 rounded-full bg-white shadow transition-all', 'left-[1.375rem]' => $on, 'left-0.5' => ! $on])></span>
</button>
