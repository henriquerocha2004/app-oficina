<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpersonationLog extends Model
{
    use HasFactory, HasUlids;

    protected $connection = 'central';

    protected $fillable = [
        'admin_user_id',
        'admin_email',
        'tenant_id',
        'tenant_name',
        'target_user_id',
        'target_user_name',
        'target_user_email',
        'started_at',
        'ended_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the admin user who performed the impersonation.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Check if impersonation is still active.
     */
    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Get duration in minutes.
     */
    public function getDurationInMinutes(): ?int
    {
        if (!$this->ended_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->ended_at);
    }

    /**
     * Scope for active impersonations.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Scope for completed impersonations.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }
}
