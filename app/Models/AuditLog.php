<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends BaseModel
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array', // Convertit automatiquement le JSON en tableau PHP
    ];

    /**
     * Relation avec l'utilisateur qui a déclenché l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper pour journaliser rapidement une action.
     */
    public static function log(string $action, array $details = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
