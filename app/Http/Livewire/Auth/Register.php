<?php

namespace App\Http\Livewire\Auth;

use App\Models\Tenant;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $companyName = '';

    public string $email = '';

    public string $password = '';

    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'companyName' => ['required', 'string', 'max:255', 'unique:tenants,name'],
            'email' => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'min:8', 'max:255'],
        ]);

        $tenant = Tenant::create([
            'name' => $this->companyName,
        ]);

        $user = User::create([
            'email' => $this->email,
            'name' => $this->name,
            'role' => 'admin',
            'password' => Hash::make($this->password),
            'tenant_id' => $tenant->id,
        ]);

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.register')->extends('layouts.auth');
    }
}
