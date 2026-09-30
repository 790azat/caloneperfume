<x-layouts::app :title="__('site.nav.works')">
    <x-page-hero :eyebrow="__('site.works.eyebrow')" :title="__('site.works.title')">{{ __('site.works.text') }}</x-page-hero>
    <section class="container-x pb-28">
        <livewire:portfolio-grid :per-page="16" />
    </section>
</x-layouts::app>
