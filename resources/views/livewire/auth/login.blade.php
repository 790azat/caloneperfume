<x-auth-card :title="__('site.auth.login_title')" :text="__('site.auth.login_text')">
    <form wire:submit="login" class="space-y-5">
        <div>
            <label class="label" for="email">{{ __('site.auth.email') }}</label>
            <input id="email" wire:model="email" type="email" class="field" autocomplete="email" autofocus>
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password">{{ __('site.auth.password') }}</label>
            <input id="password" wire:model="password" type="password" class="field" autocomplete="current-password">
            @error('password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-stone-400">
            <input type="checkbox" wire:model="remember" class="rounded border-white/20 bg-ink-800 text-gold-500 focus:ring-gold-500"> {{ __('site.auth.remember') }}
        </label>
        <button class="btn-gold w-full" wire:loading.attr="disabled">{{ __('site.auth.login') }}</button>
    </form>
    <p class="mt-6 text-center text-sm text-stone-400">{{ __('site.auth.no_account') }}
        <a href="{{ route('register') }}" wire:navigate class="text-gold-300 hover:underline">{{ __('site.nav.register') }}</a></p>
</x-auth-card>
