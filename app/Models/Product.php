<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read bool $is_low_stock
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $movements
 * @property-read int|null $movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Supplier> $suppliers
 * @property-read int|null $suppliers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product byCategory(string $category)
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product lowStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'manufacturer',
        'category',
        'stock_quantity',
        'min_stock_level',
        'unit',
        'unit_price',
        'suggested_price',
        'is_active',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'unit_price' => 'decimal:2',
        'suggested_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Category constants
    public const CATEGORY_ENGINE = 'engine';
    public const CATEGORY_SUSPENSION = 'suspension';
    public const CATEGORY_BRAKES = 'brakes';
    public const CATEGORY_ELECTRICAL = 'electrical';
    public const CATEGORY_FILTERS = 'filters';
    public const CATEGORY_FLUIDS = 'fluids';
    public const CATEGORY_TIRES = 'tires';
    public const CATEGORY_BODY_PARTS = 'body_parts';
    public const CATEGORY_INTERIOR = 'interior';
    public const CATEGORY_TOOLS = 'tools';
    public const CATEGORY_OTHER = 'other';

    // Unit constants
    public const UNIT_UNIT = 'unit';
    public const UNIT_LITER = 'liter';
    public const UNIT_KG = 'kg';
    public const UNIT_METER = 'meter';
    public const UNIT_BOX = 'box';

    /**
     * Get all available categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ENGINE,
            self::CATEGORY_SUSPENSION,
            self::CATEGORY_BRAKES,
            self::CATEGORY_ELECTRICAL,
            self::CATEGORY_FILTERS,
            self::CATEGORY_FLUIDS,
            self::CATEGORY_TIRES,
            self::CATEGORY_BODY_PARTS,
            self::CATEGORY_INTERIOR,
            self::CATEGORY_TOOLS,
            self::CATEGORY_OTHER,
        ];
    }

    /**
     * Get all available units
     */
    public static function getUnits(): array
    {
        return [
            self::UNIT_UNIT,
            self::UNIT_LITER,
            self::UNIT_KG,
            self::UNIT_METER,
            self::UNIT_BOX,
        ];
    }

    /**
     * Relationships
     */
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
            ->withPivot([
                'supplier_sku',
                'cost_price',
                'lead_time_days',
                'min_order_quantity',
                'is_preferred',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->whereNotNull('min_stock_level');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Accessors
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->min_stock_level !== null
            && $this->stock_quantity <= $this->min_stock_level;
    }
}
