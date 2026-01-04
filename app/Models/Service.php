<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service byCategory(string $category)
 * @method static \Database\Factories\ServiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withoutTrashed()
 * @mixin \Eloquent
 */
class Service extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'category',
        'estimated_time',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'estimated_time' => 'integer',
        'is_active' => 'boolean',
    ];

    // Service categories
    public const CATEGORY_MAINTENANCE = 'maintenance';
    public const CATEGORY_REPAIR = 'repair';
    public const CATEGORY_DIAGNOSTIC = 'diagnostic';
    public const CATEGORY_PAINTING = 'painting';
    public const CATEGORY_ALIGNMENT = 'alignment';
    public const CATEGORY_OTHER = 'other';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_MAINTENANCE,
            self::CATEGORY_REPAIR,
            self::CATEGORY_DIAGNOSTIC,
            self::CATEGORY_PAINTING,
            self::CATEGORY_ALIGNMENT,
            self::CATEGORY_OTHER,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the price for the service
     * This method can be extended in the future to support price rules
     */
    public function getPrice(): float
    {
        return (float) $this->base_price;
    }
}
