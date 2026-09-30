<div class="reveal card relative p-6 sm:p-10">
    @if ($sent)
        <div class="py-16 text-center" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'center' })">
            <img src="/logo.svg" alt="" class="mx-auto size-20">
            <p class="mt-6 font-display text-3xl text-stone-50">{{ __('site.form.success') }}</p>
            <button wire:click="$set('sent', false)" class="btn-ghost mt-8">{{ __('site.account.new') }}</button>
        </div>
    @else
        <form wire:submit="submit" class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="inq-name">{{ __('site.form.name') }} *</label>
                <input id="inq-name" wire:model.blur="name" class="field" autocomplete="name">
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="inq-phone">{{ __('site.form.phone') }} *</label>
                <input id="inq-phone" wire:model.blur="phone" type="tel" class="field" placeholder="+374" autocomplete="tel">
                @error('phone')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <span class="label">{{ __('site.form.event_type') }}</span>
                <div class="flex flex-wrap gap-2">
                    @foreach (__('site.form.types') as $value => $label)
                        <label @class(['cursor-pointer rounded-full border px-4 py-2 text-sm transition', 'border-stone-50 bg-stone-50 text-ink-950' => $event_type === $value, 'border-white/15 text-stone-300 hover:border-gold-500' => $event_type !== $value])>
                            <input type="radio" wire:model.live="event_type" value="{{ $value }}" class="sr-only">{{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="label" for="inq-date">{{ __('site.form.event_date') }}</label>
                <input id="inq-date" wire:model.blur="event_date" type="date" min="{{ now()->toDateString() }}" class="field [color-scheme:light]">
                @error('event_date')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="label" for="inq-email">{{ __('site.form.email') }}</label>
                <input id="inq-email" wire:model.blur="email" type="email" class="field" autocomplete="email">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="label" for="inq-message">{{ __('site.form.message') }}</label>
                <textarea id="inq-message" wire:model.blur="message" rows="4" class="field resize-none"></textarea>
                @error('message')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="btn-gold w-full sm:w-auto" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">{{ __('site.form.submit') }}</span>
                    <span wire:loading wire:target="submit">{{ __('site.form.sending') }}</span>
                    <x-icon name="arrow" class="size-4" />
                </button>
            </div>
        </form>
    @endif
</div>
