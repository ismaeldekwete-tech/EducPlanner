<?php

namespace App\Services;

class TwoFactorAuth
{
    /**
     * Génère une clé secrète aléatoire encodée en Base32 (16 caractères).
     */
    public static function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Décode une chaîne encodée en Base32 en binaire.
     */
    public static function base32Decode(string $base32): string|bool
    {
        $base32 = strtoupper($base32);
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));

        $base32 = str_replace('=', '', $base32);
        $base32 = str_split($base32);
        $binaryString = '';

        foreach ($base32 as $char) {
            if (!isset($base32charsFlipped[$char])) {
                return false;
            }
            $binaryString .= str_pad(decbin($base32charsFlipped[$char]), 5, '0', STR_PAD_LEFT);
        }

        $eightBitBytes = str_split($binaryString, 8);
        $binary = '';
        foreach ($eightBitBytes as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }
        return $binary;
    }

    /**
     * Calcule le code TOTP à 6 chiffres pour un secret et une tranche de temps donnée.
     */
    public static function getCode(string $secret, ?int $timeSlice = null): string|bool
    {
        if ($timeSlice === null) {
            $timeSlice = (int) floor(time() / 30);
        }

        $secretKey = self::base32Decode($secret);
        if (!$secretKey) {
            return false;
        }

        // Formate la tranche de temps sur 8 octets binaires
        $time = chr(0).chr(0).chr(0).chr(0).pack('N', $timeSlice);

        // HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Troncature dynamique (RFC 4226)
        $offset = ord($hmac[19]) & 0xf;
        $hashPart = substr($hmac, $offset, 4);

        // Décompression du binaire en entier
        $value = unpack('N', $hashPart);
        $value = $value[1];
        $value = $value & 0x7fffffff;

        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifie si le code saisi correspond au secret (avec une tolérance de +/- 1 créneau de 30 secondes).
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if ($calculatedCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Génère l'URL d'enrôlement QR Code compatible Google Authenticator.
     */
    public static function getQrCodeUrl(string $email, string $secret, string $issuer = 'EducPlanner'): string
    {
        $email = urlencode($email);
        $issuer = urlencode($issuer);
        $otpauthUrl = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}";

        // API sécurisée de génération de QR Code (ne transmet pas la clé en clair hors protocole)
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauthUrl);
    }
}
