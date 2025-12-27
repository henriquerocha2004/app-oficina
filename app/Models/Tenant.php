<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use HasFactory;

    /**
     * Disable automatic storage in the "data" column.
     * We're using dedicated columns instead.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'email',
            'phone',
            'subscription_plan_id',
            'subscription_status',
            'trial_ends_at',
            'is_active',
            'settings',
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'email',
        'phone',
        'subscription_plan_id',
        'subscription_status',
        'trial_ends_at',
        'is_active',
        'settings',
    ];

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isTrial(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function trialExpired(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isPast();
    }

    public function isActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' && $this->is_active;
    }

    public function isSuspended(): bool
    {
        return $this->subscription_status === 'suspended' || !$this->is_active;
    }


    public function hasFeature(string $feature): bool
    {
        if (!$this->subscriptionPlan) {
            return false;
        }

        $features = $this->subscriptionPlan->features ?? [];
        return in_array($feature, $features);
    }

    public function withinLimit(string $resource, int $currentCount): bool
    {
        if (!$this->subscriptionPlan) {
            return false;
        }

        $limits = $this->subscriptionPlan->limits ?? [];
        $limit = $limits[$resource] ?? null;

        // null means unlimited
        if ($limit === null) {
            return true;
        }

        return $currentCount < $limit;
    }

    /**
     * Get primary domain for this tenant.
     */
    public function getPrimaryDomain(): ?string
    {
        return $this->domains()->first()?->domain;
    }

    /**
     * Scope to get only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tenants on trial.
     */
    public function scopeTrial($query)
    {
        return $query->where('subscription_status', 'trial')
            ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope to get tenants with expired trial.
     */
    public function scopeExpiredTrial($query)
    {
        return $query->where('subscription_status', 'trial')
            ->where('trial_ends_at', '<=', now());
    }
}
