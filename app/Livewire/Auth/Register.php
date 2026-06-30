<?php

namespace App\Livewire\Auth;

use App\Models\AiSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    public string $business_name = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $tenant = Tenant::create([
            'name' => $this->business_name,
            'slug' => Str::slug($this->business_name) . '-' . Str::random(5),
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role' => 'owner',
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Buat default AI setting untuk tenant baru
        AiSetting::create(['tenant_id' => $tenant->id]);

        Auth::login($user);

        return redirect()->route('onboarding');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}
