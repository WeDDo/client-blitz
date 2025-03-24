<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Services\Auth\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Response;

class AuthController extends Controller
{
    private string $loginTranslate = 'auth.login';
    private string $registrationTranslate = 'auth.registration';

    public function __construct(
        private AuthService $authService,
    )
    {
    }

    public function loginShow(): Response
    {
        return inertia('auth/Login', [
            'main' => [
                'translate' => $this->loginTranslate,
            ]
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $this->authService->login($request->validated());
        } catch (AuthenticationException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        } catch (\Throwable) {
            return redirect()
                ->back()
                ->with('error', __('global.server_error'));
        }

        return redirect()->route('home');
    }

    public function registrationShow(): Response
    {
        return inertia('auth/Registration', [
            'main' => [
                'translate' => $this->registrationTranslate,
            ]
        ]);
    }

    public function registration(RegistrationRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        return redirect()->route('dashboard')->with('success', 'Registration successful.');
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        return redirect()->route('login')->with('success', 'Logged out.');
    }
}
