<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::app')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = true;

    public function login()
    {
        $this->validate();

        $key = Str::lower($this->email).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', __('auth.throttle', ['seconds' => RateLimiter::availableIn($key)]));

            return null;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key);
            $this->addError('email', __('site.auth.failed'));

            return null;
        }

        RateLimiter::clear($key);
        session()->regenerate();

        return $this->redirectIntended(Auth::user()->is_admin ? route('admin.dashboard') : route('account'));
    }

    public function render()
    {
        return view('livewire.auth.login')->title(__('site.auth.login'));
    }
}
