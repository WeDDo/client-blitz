<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw new AuthenticationException('Incorrect password', redirectTo: 'login');
        }

        $user = Auth::user();

        return [
            'user' => $user,
        ];
    }

    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return [
            'user' => $user,
        ];
    }
}


