<?php

namespace App\Models;

use App\Models\Vehicle;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read Collection<int, Vehicle> $cars
 * @property-read int|null $cars_count
 * @method static ClientFactory factory($count = null, $state = [])
 * @method static Builder<static>|Client newModelQuery()
 * @method static Builder<static>|Client newQuery()
 * @method static Builder<static>|Client onlyTrashed()
 * @method static Builder<static>|Client query()
 * @method static Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Client withoutTrashed()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasUlids;

    protected $table = 'clients';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'email',
        'document_number',
        'street',
        'city',
        'state',
        'zip_code',
        'phone',
        'observations',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'client_id', 'id');
    }
}
