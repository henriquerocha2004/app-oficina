<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\StockMovementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement query()
 * @mixin \Eloquent
 */
class StockMovement extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'reason',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
    ];

    // Movement type constants
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';

    // Reason constants
    public const REASON_PURCHASE = 'purchase';
    public const REASON_SALE = 'sale';
    public const REASON_ADJUSTMENT = 'adjustment';
    public const REASON_LOSS = 'loss';
    public const REASON_RETURN = 'return';
    public const REASON_TRANSFER = 'transfer';
    public const REASON_INITIAL = 'initial';
    public const REASON_OTHER = 'other';

    /**
     * Get all available reasons
     */
    public static function getReasons(): array
    {
        return [
            self::REASON_PURCHASE,
            self::REASON_SALE,
            self::REASON_ADJUSTMENT,
            self::REASON_LOSS,
            self::REASON_RETURN,
            self::REASON_TRANSFER,
            self::REASON_INITIAL,
            self::REASON_OTHER,
        ];
    }

    /**
     * Relationships
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
