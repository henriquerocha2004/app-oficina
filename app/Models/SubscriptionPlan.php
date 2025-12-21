<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'limits',
        'features',
        'is_active',
        'is_visible',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'limits' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tenants using this plan.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get limit for a specific resource.
     */
    public function getLimit(string $resource): ?int
    {
        return $this->limits[$resource] ?? null;
    }

    /**
     * Check if plan has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Check if plan is free.
     */
    public function isFree(): bool
    {
        return $this->price_monthly == 0 && $this->price_yearly == 0;
    }

    /**
     * Get yearly savings amount.
     */
    public function getYearlySavings(): float
    {
        if ($this->price_monthly == 0 || $this->price_yearly == 0) {
            return 0;
        }

        $monthlyTotal = $this->price_monthly * 12;
        return $monthlyTotal - $this->price_yearly;
    }

    /**
     * Get yearly savings percentage.
     */
    public function getYearlySavingsPercentage(): int
    {
        if ($this->price_monthly == 0 || $this->price_yearly == 0) {
            return 0;
        }

        $monthlyTotal = $this->price_monthly * 12;
        $savings = $monthlyTotal - $this->price_yearly;
        return (int) (($savings / $monthlyTotal) * 100);
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only visible plans (for public display).
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
