<x-auth-card :title="__('site.auth.register_title')" :text="__('site.auth.register_text')">
    <form wire:submit="register" class="space-y-5">
        <div>
            <label class="label" for="name">{{ __('site.auth.name') }}</label>
            <input id="name" wire:model.blur="name" class="field" autocomplete="name" autofocus>
            @error('name')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="email">{{ __('site.auth.email') }}</label>
            <input id="email" wire:model.blur="email" type="email" class="field" autocomplete="email">
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password">{{ __('site.auth.password') }}</label>
            <input id="password" wire:model="password" type="password" class="field" autocomplete="new-password">
            @error('password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password_confirmation">{{ __('site.auth.password_confirmation') }}</label>
            <input id="password_confirmation" wire:model="password_confirmation" type="password" class="field" autocomplete="new-password">
        </div>
        <button class="btn-gold w-full" wire:loading.attr="disabled">{{ __('site.auth.register') }}</button>
    </form>
    <p class="mt-6 text-center text-sm text-stone-400">{{ __('site.auth.have_account') }}
        <a href="{{ route('login') }}" wire:navigate class="text-gold-300 hover:underline">{{ __('site.nav.login') }}</a></p>
</x-auth-card>
