@php($settings = \App\Models\Setting::values())
<x-layouts::app :title="__('site.nav.contact')">
    <x-page-hero :eyebrow="__('site.form.eyebrow')" :title="__('site.form.title')">{{ __('site.form.text') }}</x-page-hero>
    <section class="container-x grid gap-10 pb-28 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <livewire:inquiry-form />
        </div>
        <aside class="space-y-4">
            @foreach ([['phone', 'phone', 'tel:'.preg_replace('/[^\d+]/', '', $settings['phone'])], ['mail', 'email', 'mailto:'.$settings['email']], ['pin', 'address', null], ['calendar', 'hours', null]] as [$icon, $key, $href])
                <div class="reveal card flex items-center gap-5 p-6">
                    <span class="grid size-12 shrink-0 place-items-center rounded-full border border-gold-500/40 text-gold-500"><x-icon :name="$icon" /></span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold tracking-[0.2em] text-stone-500 uppercase">{{ __('site.contact.'.$key) }}</p>
                        @if ($href)
                            <a href="{{ $href }}" class="block truncate font-display text-2xl text-stone-50 hover:text-gold-500">{{ $settings[$key] }}</a>
                        @else
                            <p class="font-display text-2xl text-stone-50">{{ $settings[$key] }}@if ($key === 'hours') <span class="font-sans text-sm text-stone-400">· {{ __('site.visit.daily') }}</span>@endif</p>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="reveal card p-6">
                <p class="text-[11px] font-semibold tracking-[0.2em] text-stone-500 uppercase">{{ __('site.contact.follow') }}</p>
                <div class="mt-4 flex gap-3">
                    @if ($settings['instagram'])<a href="{{ $settings['instagram'] }}" target="_blank" rel="noopener" class="grid size-12 place-items-center rounded-full border border-white/15 transition hover:border-gold-500 hover:bg-gold-500 hover:text-[#fff]" aria-label="Instagram"><x-social network="instagram" /></a>@endif
                    @if ($settings['facebook'])<a href="{{ $settings['facebook'] }}" target="_blank" rel="noopener" class="grid size-12 place-items-center rounded-full border border-white/15 transition hover:border-gold-500 hover:bg-gold-500 hover:text-[#fff]" aria-label="Facebook"><x-social network="facebook" /></a>@endif
                </div>
            </div>
        </aside>
    </section>
</x-layouts::app>
