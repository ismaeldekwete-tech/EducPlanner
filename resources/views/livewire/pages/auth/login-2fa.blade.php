<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\TwoFactorAuth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';
    public string $errorMessage = '';
    public string $twoFactorType = 'none';
    public string $email = '';

    public function mount(): void
    {
        $userId = Session::get('2fa.user_id');
        if (!$userId) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::findOrFail($userId);
        $this->twoFactorType = $user->two_factor_type;
        $this->email = $user->email;
    }

    public function verify(): void
    {
        $this->validate([
            'code' => 'required|string|min:6|max:6',
        ]);

        $userId = Session::get('2fa.user_id');
        if (!$userId) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::findOrFail($userId);
        $verified = false;

        if ($this->twoFactorType === 'totp') {
            // Vérification classique TOTP
            $verified = TwoFactorAuth::verifyCode($user->two_factor_secret, $this->code);

            // Code de récupération
            if (!$verified && !empty($user->two_factor_recovery_codes)) {
                $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                if (is_array($recoveryCodes) && in_array($this->code, $recoveryCodes)) {
                    $recoveryCodes = array_diff($recoveryCodes, [$this->code]);
                    $user->update([
                        'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
                    ]);
                    $verified = true;
                }
            }
        } else {
            // Vérification OTP Email
            $verified = $user->email_otp_code === $this->code &&
                        $user->email_otp_expires_at &&
                        now()->lessThanOrEqualTo($user->email_otp_expires_at);
        }

        if ($verified) {
            if ($this->twoFactorType === 'email') {
                $user->update([
                    'email_otp_code' => null,
                    'email_otp_expires_at' => null,
                ]);
            }

            Auth::login($user, Session::get('2fa.remember', false));
            Session::forget(['2fa.user_id', '2fa.remember']);
            Session::regenerate();

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } else {
            $this->errorMessage = __('Le code saisi est incorrect ou a expiré.');
        }
    }

    public function resendCode(): void
    {
        if ($this->twoFactorType !== 'email') {
            return;
        }

        $userId = Session::get('2fa.user_id');
        if (!$userId) {
            return;
        }

        $user = User::findOrFail($userId);

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'email_otp_code' => $code,
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Bonjour {$user->name},\n\nVotre nouveau code de connexion à usage unique (OTP) est : {$code}\n\nCe code est valide pendant 10 minutes.\n\nCordialement,\nL'administration.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject("EducPlanner : Votre nouveau code de sécurité OTP");
                }
            );
            $this->errorMessage = '';
            session()->flash('status', __('Un nouveau code a été envoyé sur votre adresse e-mail.'));
        } catch (\Exception $e) {
            $this->errorMessage = __("Erreur lors de l'envoi de l'e-mail.");
        }
    }
}; ?>

<div class="min-h-[400px] flex flex-col justify-center py-6 sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
        <div class="relative px-4 py-10 bg-white dark:bg-gray-800 shadow-lg sm:rounded-3xl sm:p-20 text-gray-900 dark:text-gray-100">
            <div class="max-w-md mx-auto">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white flex items-center justify-center gap-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Double Facteur (2FA)') }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        @if($twoFactorType === 'totp')
                            {{ __('Saisissez le code à 6 chiffres généré par votre application Google Authenticator.') }}
                        @else
                            {{ __('Saisissez le code de sécurité à 6 chiffres envoyé par e-mail à') }} <strong>{{ $email }}</strong>.
                        @endif
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400 p-3 bg-green-50 dark:bg-green-950 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mb-4 text-sm font-medium text-red-600 dark:text-red-400 p-3 bg-red-50 dark:bg-red-950 rounded-lg">
                        {{ $errorMessage }}
                    </div>
                @endif

                <form wire:submit="verify" class="space-y-6">
                    <div>
                        <x-input-label for="code" :value="__('Code de sécurité')" />
                        <x-text-input wire:model="code" id="code" class="block mt-1 w-full text-center tracking-widest text-2xl font-bold dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="code" required autofocus placeholder="000000" autocomplete="one-time-code" maxlength="6" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    <div class="flex flex-col gap-4 mt-6">
                        <x-primary-button class="w-full justify-center">
                            {{ __('Vérifier') }}
                        </x-primary-button>

                        @if($twoFactorType === 'email')
                            <button type="button" wire:click="resendCode" class="text-sm text-blue-600 dark:text-blue-400 hover:underline text-center">
                                {{ __('Renvoyer le code') }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
