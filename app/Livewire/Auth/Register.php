<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function register()
    {
        $data = $this->validate();

        $user = User::create([
            ...$data,
            // The email in ADMIN_EMAIL becomes an admin as soon as it registers.
            'is_admin' => filled(config('app.admin_email'))
                && strcasecmp($data['email'], config('app.admin_email')) === 0,
        ]);

        Auth::login($user, true);
        session()->regenerate();

        return $this->redirect($user->is_admin ? route('admin.dashboard') : route('account'));
    }

    public function render()
    {
        return view('livewire.auth.register')->title(__('site.auth.register'));
    }
}
