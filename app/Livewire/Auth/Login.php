<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        session()->regenerate();

        $tenant = Auth::user()->tenant;
        if ($tenant && ! $tenant->isOnboarded()) {
            return redirect()->route('onboarding');
        }

        return redirect()->route('dashboard.inbox');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
