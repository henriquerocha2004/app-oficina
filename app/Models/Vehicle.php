<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasUlids;

    protected $table = 'vehicles';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'brand',
        'model',
        'year',
        'vehicle_type',
        'license_plate',
        'vin',
        'transmission',
        'color',
        'cilinder_capacity',
        'mileage',
        'observations',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
