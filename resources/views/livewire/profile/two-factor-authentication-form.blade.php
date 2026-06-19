<?php

use App\Services\TwoFactorAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public string $twoFactorType = 'none';
    public string $selectedType = 'none';
    public string $secret = '';
    public string $qrCodeUrl = '';
    public string $confirmCode = '';
    public array $recoveryCodes = [];
    public string $step = 'idle'; // idle, setup_totp, setup_email, show_recovery
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->twoFactorType = $user->two_factor_type ?? 'none';
        $this->selectedType = $user->two_factor_type ?? 'none';
    }

    public function selectType(string $type): void
    {
        $this->selectedType = $type;
        $this->errorMessage = '';
        $this->successMessage = '';

        if ($type === 'none') {
            $this->disable2Fa();
        } elseif ($type === 'totp') {
            $this->startTotpSetup();
        } elseif ($type === 'email') {
            $this->startEmailSetup();
        }
    }

    public function startTotpSetup(): void
    {
        $user = Auth::user();

        // Si déjà activé et pas en cours de modification, on ne fait rien
        if ($this->twoFactorType === 'totp') {
            $this->step = 'idle';
            return;
        }

        $this->secret = TwoFactorAuth::generateSecret();
        $this->qrCodeUrl = TwoFactorAuth::getQrCodeUrl($user->email, $this->secret);
        $this->step = 'setup_totp';
        $this->confirmCode = '';
    }

    public function confirmTotp(): void
    {
        $this->validate([
            'confirmCode' => 'required|string|min:6|max:6',
        ]);

        $user = Auth::user();

        if (TwoFactorAuth::verifyCode($this->secret, $this->confirmCode)) {
            // Générer des codes de récupération de secours
            $codes = [];
            for ($i = 0; $i < 8; $i++) {
                $codes[] = str_pad((string)random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            }
            $this->recoveryCodes = $codes;

            $user->update([
                'two_factor_secret' => $this->secret,
                'two_factor_confirmed_at' => now(),
                'two_factor_type' => 'totp',
                'two_factor_recovery_codes' => encrypt(json_encode($codes)),
            ]);

            $this->twoFactorType = 'totp';
            $this->step = 'show_recovery';
            $this->successMessage = __('L\'authentification 2FA par Google Authenticator a été activée avec succès !');

            \App\Models\AuditLog::log('2FA_ACTIVATED', ['method' => 'totp']);
        } else {
            $this->errorMessage = __('Le code de confirmation est incorrect. Veuillez réessayer.');
        }
    }

    public function startEmailSetup(): void
    {
        $user = Auth::user();

        if ($this->twoFactorType === 'email') {
            $this->step = 'idle';
            return;
        }

        // Générer un code temporaire pour valider l'email
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'email_otp_code' => $code,
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Bonjour {$user->name},\n\nPour confirmer l'activation de la double authentification par e-mail sur votre compte EducPlanner, veuillez saisir le code de confirmation suivant : {$code}\n\nCe code est valide pendant 10 minutes.\n\nCordialement,\nL'administration.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject("EducPlanner : Confirmation d'activation 2FA");
                }
            );
            $this->step = 'setup_email';
            $this->confirmCode = '';
            $this->successMessage = __('Un code de validation a été envoyé à votre adresse e-mail.');
        } catch (\Exception $e) {
            $this->errorMessage = __("Impossible d'envoyer l'e-mail de validation. Veuillez vérifier votre configuration.");
            $this->selectedType = $this->twoFactorType;
        }
    }

    public function confirmEmail(): void
    {
        $this->validate([
            'confirmCode' => 'required|string|min:6|max:6',
        ]);

        $user = Auth::user();

        if ($user->email_otp_code === $this->confirmCode &&
            $user->email_otp_expires_at &&
            now()->lessThanOrEqualTo($user->email_otp_expires_at)) {

            $user->update([
                'two_factor_type' => 'email',
                'email_otp_code' => null,
                'email_otp_expires_at' => null,
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => now(),
            ]);

            $this->twoFactorType = 'email';
            $this->step = 'idle';
            $this->successMessage = __('L\'authentification 2FA par code e-mail a été activée.');

            \App\Models\AuditLog::log('2FA_ACTIVATED', ['method' => 'email']);
        } else {
            $this->errorMessage = __('Le code de validation est incorrect ou a expiré.');
        }
    }

    public function disable2Fa(): void
    {
        $user = Auth::user();

        $user->update([
            'two_factor_type' => 'none',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
            'email_otp_code' => null,
            'email_otp_expires_at' => null,
        ]);

        $this->twoFactorType = 'none';
        $this->selectedType = 'none';
        $this->step = 'idle';
        $this->successMessage = __('L\'authentification double facteur (2FA) a été désactivée.');

        \App\Models\AuditLog::log('2FA_DEACTIVATED');
    }

    public function closeRecoveryStep(): void
    {
        $this->step = 'idle';
        $this->recoveryCodes = [];
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Authentification Double Facteur (2FA)') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Renforcez la sécurité de votre compte en activant l\'authentification à deux facteurs. Les rôles administratifs doivent impérativement l\'activer.') }}
        </p>
    </header>

    @if ($successMessage)
        <div class="p-4 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ $successMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="p-4 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300 rounded-lg text-sm">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Option Aucun -->
        <label class="relative flex flex-col p-5 bg-white dark:bg-gray-800 border-2 rounded-xl cursor-pointer focus:outline-none transition hover:shadow {{ $selectedType === 'none' ? 'border-indigo-600 dark:border-indigo-500 shadow-sm' : 'border-gray-200 dark:border-gray-700' }}">
            <input type="radio" name="2fa_type" value="none" wire:click="selectType('none')" class="sr-only">
            <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                {{ __('Désactivé') }}
            </span>
            <span class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Aucune protection supplémentaire. Déconseillé.') }}
            </span>
        </label>

        <!-- Option OTP Email -->
        <label class="relative flex flex-col p-5 bg-white dark:bg-gray-800 border-2 rounded-xl cursor-pointer focus:outline-none transition hover:shadow {{ $selectedType === 'email' ? 'border-indigo-600 dark:border-indigo-500 shadow-sm' : 'border-gray-200 dark:border-gray-700' }}">
            <input type="radio" name="2fa_type" value="email" wire:click="selectType('email')" class="sr-only">
            <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ __('Code OTP par E-mail') }}
            </span>
            <span class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Un code temporaire à 6 chiffres vous sera envoyé par e-mail à chaque connexion.') }}
            </span>
        </label>

        <!-- Option TOTP Google Authenticator -->
        <label class="relative flex flex-col p-5 bg-white dark:bg-gray-800 border-2 rounded-xl cursor-pointer focus:outline-none transition hover:shadow {{ $selectedType === 'totp' ? 'border-indigo-600 dark:border-indigo-500 shadow-sm' : 'border-gray-200 dark:border-gray-700' }}">
            <input type="radio" name="2fa_type" value="totp" wire:click="selectType('totp')" class="sr-only">
            <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                {{ __('Google Authenticator') }}
            </span>
            <span class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Protection maximale. Scannez le QR Code avec une application d\'authentification.') }}
            </span>
        </label>
    </div>

    <!-- Étape : Configuration de Google Authenticator -->
    @if ($step === 'setup_totp')
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl space-y-6">
            <h3 class="font-medium text-gray-900 dark:text-white">{{ __('Activer Google Authenticator (TOTP)') }}</h3>

            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- QR Code -->
                <div class="p-3 bg-white rounded-lg shadow-sm">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code 2FA" class="w-48 h-48">
                </div>

                <div class="space-y-3 max-w-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('1. Scannez ce QR Code avec votre application d\'authentification (Google Authenticator, Microsoft Authenticator, Duo, etc.).') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('2. Si vous ne pouvez pas scanner, vous pouvez saisir manuellement cette clé secrète :') }}
                        <br>
                        <code class="px-2 py-1 bg-white dark:bg-gray-800 font-mono text-indigo-600 dark:text-indigo-400 rounded border border-gray-200 dark:border-gray-700 font-bold select-all tracking-wider">{{ $secret }}</code>
                    </p>
                </div>
            </div>

            <!-- Formulaire de validation du code -->
            <div class="max-w-xs space-y-3">
                <x-input-label for="confirmCode" :value="__('Saisissez le code généré à 6 chiffres pour valider :')" />
                <x-text-input wire:model="confirmCode" id="confirmCode" type="text" class="block w-full text-center tracking-widest text-xl font-bold font-mono" placeholder="000000" maxlength="6" />
                <x-primary-button wire:click="confirmTotp" class="w-full justify-center">
                    {{ __('Confirmer et Activer') }}
                </x-primary-button>
            </div>
        </div>
    @endif

    <!-- Étape : Configuration de l'E-mail OTP -->
    @if ($step === 'setup_email')
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl space-y-6 max-w-md">
            <h3 class="font-medium text-gray-900 dark:text-white">{{ __('Activer la double authentification par e-mail') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Veuillez saisir le code de sécurité à 6 chiffres envoyé sur votre adresse e-mail pour confirmer l\'activation :') }}
            </p>

            <div class="space-y-3">
                <x-text-input wire:model="confirmCode" type="text" class="block w-full text-center tracking-widest text-xl font-bold font-mono" placeholder="000000" maxlength="6" />
                <div class="flex gap-4">
                    <x-primary-button wire:click="confirmEmail" class="w-full justify-center">
                        {{ __('Confirmer et Activer') }}
                    </x-primary-button>
                    <button type="button" wire:click="startEmailSetup" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Renvoyer le code') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Étape : Affichage des codes de récupération (Secours) -->
    @if ($step === 'show_recovery')
        <div class="p-6 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-900 rounded-xl space-y-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div class="space-y-1">
                    <h3 class="font-semibold text-indigo-900 dark:text-indigo-300">{{ __('Codes de récupération de secours') }}</h3>
                    <p class="text-sm text-indigo-700 dark:text-indigo-400">
                        {{ __('Conservez précieusement ces codes dans un endroit sûr (ex: gestionnaire de mots de passe). Ils vous permettront d\'accéder à votre compte si vous perdez l\'accès à votre application Google Authenticator.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach ($recoveryCodes as $code)
                    <div class="p-2 text-center font-mono font-bold bg-white dark:bg-gray-800 border border-indigo-100 dark:border-indigo-900 rounded shadow-sm text-gray-900 dark:text-white select-all">
                        {{ $code }}
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                <x-primary-button wire:click="closeRecoveryStep">
                    {{ __('J\'ai sauvegardé ces codes') }}
                </x-primary-button>
            </div>
        </div>
    @endif
</section>
