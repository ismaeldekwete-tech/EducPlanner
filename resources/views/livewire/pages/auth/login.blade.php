<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $user = \App\Models\User::where('email', $this->form->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->form->password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => __('auth.failed'),
            ]);
        }

        // Si le 2FA est activé
        if ($user->two_factor_type !== 'none') {
            Session::put('2fa.user_id', $user->id);
            Session::put('2fa.remember', $this->form->remember);

            if ($user->two_factor_type === 'email') {
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update([
                    'email_otp_code' => $code,
                    'email_otp_expires_at' => now()->addMinutes(10),
                ]);

                try {
                    \Illuminate\Support\Facades\Mail::raw(
                        "Bonjour {$user->name},\n\nVotre code de connexion à usage unique (OTP) est : {$code}\n\nCe code est valide pendant 10 minutes.\n\nCordialement,\nL'administration.",
                        function ($message) use ($user) {
                            $message->to($user->email)
                                ->subject("EducPlanner : Votre code de sécurité OTP");
                        }
                    );
                } catch (\Exception $e) {
                    // Ignorer si la configuration mail locale n'est pas opérationnelle
                }
            }

            $this->redirect(route('login.2fa'), navigate: true);
            return;
        }

        // Sinon connexion normale
        \Illuminate\Support\Facades\Auth::login($user, $this->form->remember);

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        @csrf
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
