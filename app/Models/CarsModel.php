<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarsModel extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasUlids;

    protected $table = 'cars';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'brand',
        'model',
        'year',
        'type',
        'licence_plate',
        'vin',
        'transmission',
        'color',
        'cilinder_capacity',
        'mileage',
        'observations',
    ];

    public function clients(): BelongsTo
    {
        return $this->belongsTo(ClientModel::class, 'client_id', 'id');
    }
}
